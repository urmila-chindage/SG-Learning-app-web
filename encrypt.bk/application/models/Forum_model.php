<?php 
Class Forum_model extends CI_Model
{	
    function __construct()
    {
        parent::__construct();
    }
    public function check_topic_slug($slug, $id=false)
	{
		if($id)
		{
			$this->db->where('id !=', $id);
		}
		$this->db->where('topic_slug', $slug);
		$this->db->where('topic_account_id',config_item('id'));
		
		return (bool) $this->db->count_all_results('forum_topics');
	}
	
	public function validate_topic_slug($slug, $id=false, $count=false)
	{
		if($this->check_topic_slug($slug.$count, $id))
		{
			if(!$count)
			{
				$count	= 1;
			}
			else
			{
				$count++;
			}
			return $this->validate_topic_slug($slug, $id, $count);
		}
		else
		{
			return $slug.$count;
		}
	}
	public function check_forum_slug($slug, $id=false)
	{
		if($id)
		{
			$this->db->where('id !=', $id);
		}
		$this->db->where('forum_account_id',config_item('id'));
		$this->db->where('forum_slug', $slug);
		
		return (bool) $this->db->count_all_results('forums');
	}
	public function get_reported_comment($comment_id){
    	$this->db->select('*');
    	$this->db->where('id',$comment_id);
		return $this->db->get('forum_topic_comments')->row_array();
	}
	public function validate_forum_slug($slug, $id=false, $count=false)
	{
		if($this->check_forum_slug($slug.$count, $id))
		{
			if(!$count)
			{
				$count	= 1;
			}
			else
			{
				$count++;
			}
			return $this->validate_forum_slug($slug, $id, $count);
		}
		else
		{
			return $slug.$count;
		}
	}
	public function update_topic_views($topic_id){
		$this->db->select('views');
		$this->db->where('id',$topic_id);
		$views = $this->db->get('forum_topics')->row('views');
		//print_r($views);die;
		$views++;
		$val = array('views'=>$views);
		$this->db->where('id',$topic_id);
		$this->db->update('forum_topics',$val);
	}

    public function get_forums($limit,$offset){
    	$this->db->limit($limit, $offset);
    	$this->db->where('forum_account_id',config_item('id'));
    	$this->db->where('forum_deleted',0);
    	$this->db->order_by('forum_created', 'DESC');
		return $this->db->get('forums')->result();
		
	}
	public function create_forum($args){
		$args['forum_account_id'] = config_item('id');
		return $this->db->insert('forums', $args);
		
	}
	public function count_forum_posts($forum_id) {
		
		$this->db->select('forum_topic_comments.id');
		$this->db->from('forum_topic_comments');
		$this->db->join('forum_topics', 'forum_topic_comments.topic_id = forum_topics.id');
		$this->db->where('forum_topics.topic_account_id',config_item('id'));
		$this->db->where('forum_topic_comments.comment_account_id',config_item('id'));
		$this->db->where('forum_topic_comments.comment_deleted',0);
		$this->db->where('forum_topic_comments.comment_blocked',0);
		$this->db->where('forum_topics.forum_id', $forum_id);
		$this->db->group_by('forum_topic_comments.id');
		return count($this->db->get()->result());
		
	}
	public function get_forum_latest_topic($forum_id) {
		
		$this->db->from('forum_topics');
		$this->db->where('forum_id', $forum_id);
		$this->db->where('topic_account_id',config_item('id'));
		$this->db->where('topic_deleted',0);
		$this->db->order_by('topic_created', 'DESC');
		$this->db->limit(1);
		return $this->db->get()->row_array();
		
	}
	
	public function get_forum_id_from_forum_slug($slug) {
		
		$this->db->select('id');
		$this->db->from('forums');
		$this->db->where('forum_account_id',config_item('id'));
		$this->db->where('forum_slug', $slug);
		return $this->db->get()->row('id');
		
	}
	public function get_forum($forum_id) {
		
		$this->db->from('forums');
		$this->db->where('forum_account_id',config_item('id'));
		$this->db->where('forum_deleted',0);
		$this->db->where('id', $forum_id);
		return $this->db->get()->row();
		
	}
	function forum_count()
    {	
    	$this->db->where('forum_account_id',config_item('id'));
    	$this->db->where('forum_deleted',0);
        $result = $this->db->count_all_results("forums");
        return $result;
    }    
	public function get_topic($topic_id) {
		
		$this->db->from('forum_topics');
		$this->db->where('topic_account_id',config_item('id'));
		$this->db->where('topic_deleted',0);
		$this->db->where('id', $topic_id);
		return $this->db->get()->row();
		//echo $this->db->last_query();die;
		
	}
	public function get_forum_topics($forum_id,$limit,$offset) {
		$this->db->from('forum_topics');
		$this->db->limit($limit,$offset);
		$this->db->order_by('topic_created', 'DESC');
		$this->db->where('topic_account_id',config_item('id'));
		$this->db->where('topic_deleted',0);
		$this->db->where('forum_id', $forum_id);
		return $this->db->get()->result();
		
	}
	public function topic_count($forum_id){
		$this->db->where('forum_id', $forum_id);
		$this->db->where('topic_account_id',config_item('id'));
		$this->db->where('topic_deleted',0);
		$result = $this->db->count_all_results("forum_topics");
        return $result;
	}
	public function get_username_from_user_id($user_id) {
		
		$this->db->select('us_name');
		$this->db->from('users');
		$this->db->where('id', $user_id);

		return $this->db->get()->row_array('us_name');
		
	}
	public function get_userimage_from_user_id($user_id) {
		
		$this->db->select('us_image');
		$this->db->from('users');
		$this->db->where('id', $user_id);

		return $this->db->get()->row('us_image');
		
	}
	public function get_posts($topic_id,$limit,$offset) {
		
		$this->db->from('forum_topic_comments');
		$this->db->limit($limit,$offset);
		$this->db->order_by('comment_created', 'DESC');
		$this->db->where('comment_account_id',config_item('id'));
		$this->db->where('comment_deleted',0);
		$this->db->where('comment_blocked',0);
		$this->db->where('topic_id', $topic_id);
		$this->db->where('parent_id',0);
		return $this->db->get()->result();
		
	}
	public function post_count($topic_id){
		$this->db->where('topic_id', $topic_id);
		$this->db->where('parent_id', 0);
		$this->db->where('comment_account_id',config_item('id'));
		$this->db->where('comment_deleted',0);
		$this->db->where('comment_blocked',0);
		$result = $this->db->count_all_results("forum_topic_comments");
        return $result;
	}
	public function post_total_count($topic_id){
		$this->db->where('topic_id', $topic_id);
		$this->db->where('comment_account_id',config_item('id'));
		$this->db->where('comment_deleted',0);
		$this->db->where('comment_blocked',0);
		$result = $this->db->count_all_results("forum_topic_comments");
        return $result;
	}
	public function get_topic_latest_post($topic_id) {
		
		$this->db->from('forum_topic_comments');
		$this->db->where('topic_id', $topic_id);
		$this->db->where('comment_account_id',config_item('id'));
		$this->db->where('comment_blocked',0);
		$this->db->where('comment_deleted',0);
		$this->db->order_by('comment_created', 'DESC');
		$this->db->limit(1);
		return $this->db->get()->row();
		
	}
	public function create_topic($arg){
		$arg['topic_account_id'] = config_item('id');
		if ($this->db->insert('forum_topics', $arg)) {
			$topic_id = $this->db->insert_id();
			return $topic_id;
		}
		return false;
		
	}
	public function create_post($topic_id, $user_id, $content,$parent=false) {
		
		$data = array(
			'topic_comment'    => $content,
			'user_id'    => $user_id,
			'topic_id'   => $topic_id,
			'comment_created' => date('Y-m-j H:i:s'),
			'comment_account_id' => config_item('id')
		);
		
		if ($this->db->insert('forum_topic_comments', $data)) {
			
			$data = array('topic_modified' => date('Y-m-j H:i:s'));
			$this->db->where('id', $topic_id);
			return $this->db->update('forum_topics', $data);
			
		}
		return false;
		
	}

	public function report_comment($row){
		if ($this->db->insert('forum_reported_comments', $row)) {
			return true;
		}
	}

	public function get_comment_stat($param =array()){
		$this->db->select('*');
		if(isset($param['user_id'])){
			$this->db->where('user_id',$param['user_id']);
		}

		if(isset($param['comment_id'])){
			$this->db->where('comment_id',$param['comment_id']);
		}

		if(isset($param['count'])){
			return $this->db->get('forum_reported_comments')->num_rows();
		}else{
			return $this->db->get('forum_reported_comments');
		}
	}

	public function get_topic_id_from_topic_slug($topic_slug) {
		
		$this->db->select('id');
		$this->db->from('forum_topics');
		$this->db->where('topic_account_id',config_item('id'));
		$this->db->where('topic_slug', $topic_slug);
		return $this->db->get()->row('id');
		
	}
	public function delete_forum_topic_comments($topic_id){
		$arr = array('topic_id' => $topic_id,'comment_account_id'=>config_item('id'));
		$this->db->where($arr);
		$this->db->update('forum_topic_comments',array('comment_deleted'=>1));
	}
	public function delete_forum_topic_comment($comment_id){
		$arr = array('id' => $comment_id,'comment_account_id'=>config_item('id'));
		$this->db->where($arr);
		$this->db->update('forum_topic_comments',array('comment_deleted'=>1));
	}
	public function delete_forum_topics($forum_id){
		$arr = array('forum_id' => $forum_id,'topic_account_id'=>config_item('id'));
		$this->db->where($arr);
		$this->db->update('forum_topics',array('topic_deleted'=>1));
	}
	public function delete_forum_topic($topic_id){
		$arr = array('id' => $topic_id,'topic_account_id'=>config_item('id'));
		$this->db->where($arr);
		$this->db->update('forum_topics',array('topic_deleted'=>1));
	}
	public function delete_forum($forum_id){
		$arr = array('id' => $forum_id,'forum_account_id'=>config_item('id'));
		$this->db->where($arr);
		$this->db->update('forums',array('forum_deleted'=>1));
	}
	public function forum_update($forum_id){
		$arr = array('forum_modified'=>date('Y-m-j H:i:s'));
		$this->db->where('id', $forum_id);
		$this->db->update('forums', $arr);

	}
	public function topic_update($topic_id){
		$arr = array('topic_modified'=>date('Y-m-j H:i:s'));
		$this->db->where('id', $topic_id);
		$this->db->update('forum_topics', $arr);

	}
	public function update_comment($parent_id){
		$arr = array('comment_edited'=>date('Y-m-j H:i:s'));
		$this->db->where('id', $parent_id);
		$this->db->update('forum_topic_comments', $arr);
	}

	//Search methods
	public function search_topic_count($forum_id,$search_term){
		$this->db->where('forum_id', $forum_id);
		$this->db->where("topic_name LIKE '%".$search_term."%'");
		$this->db->where('topic_account_id',config_item('id'));
		$this->db->where('topic_deleted',0);
		$result = $this->db->count_all_results("forum_topics");
        return $result;
	}
	public function get_search_forum_topics($forum_id,$search_term,$limit,$offset) {
		$this->db->from('forum_topics');
		$this->db->limit($limit,$offset);
		$this->db->where("topic_name LIKE '%".$search_term."%'");
		$this->db->order_by('topic_created', 'DESC');
		$this->db->where('topic_account_id',config_item('id'));
		$this->db->where('forum_id', $forum_id);
		$this->db->where('topic_deleted',0);
		return $this->db->get()->result();
		
	}
	public function get_child_comments($parent_id,$offset,$limit){
		$this->db->from('forum_topic_comments');
		$this->db->limit($limit,$offset);
		//$this->db->order_by('comment_created', 'ASC');
		$this->db->where('comment_deleted',0);
		$this->db->where('comment_blocked',0);
		$this->db->where('comment_account_id',config_item('id'));
		$this->db->where('parent_id', $parent_id);
		return $this->db->get()->result();
	}
	public function get_ajax_child_comments($parent_id,$offset,$limit){
		$this->db->from('forum_topic_comments');
		$this->db->limit($limit,$offset);
		$this->db->order_by('comment_created', 'ASC');
		$this->db->where('comment_account_id',config_item('id'));
		$this->db->where('comment_deleted',0);
		$this->db->where('comment_blocked',0);
		$this->db->where('parent_id', $parent_id);
		return $this->db->get()->result();
	}
	public function add_child_comment($arr){
		$arr['comment_account_id'] = config_item('id');
		$this->db->insert('forum_topic_comments',$arr);
		$id = $this->db->insert_id();
		$q = $this->db->get_where('forum_topic_comments', array('id' => $id));
		return $q->row();
	}
	public function get_users_in_topic($topic_id){
		$this->db->select('user_id');
		$this->db->from('forum_topic_comments');
		$this->db->where('comment_account_id',config_item('id'));
		$this->db->where('topic_id',$topic_id);
		return $this->db->get()->result();	
	}
	public function get_user_cred($user){
		$this->db->from('users');
		$this->db->where('id', $user);
		return $this->db->get()->row();	
	}
	public function get_user_role_from_user_id($user_id) {
		
		$this->db->select('us_role_id');
		$this->db->from('users');
		$this->db->where('id', $user_id);

		return $this->db->get()->row('us_role_id');
		
	}
	public function get_user_role_from_role_id($role_id){
		$this->db->select('rl_name');
		$this->db->from('roles');
		$this->db->where('id', $role_id);

		return $this->db->get()->row('rl_name');
	}
	public function get_user_post_count($user_id){
		$this->db->where('user_id', $user_id);
		$this->db->where('topic_account_id',config_item('id'));
		$this->db->where('topic_deleted','0');
		$result = $this->db->count_all_results("forum_topics");
        return $result;
	}
	public function get_child_comment_count($comment_id){
		$this->db->where('parent_id', $comment_id);
		$this->db->where('comment_account_id',config_item('id'));
		$this->db->where('comment_deleted',0);
		$this->db->where('comment_blocked',0);
		$result = $this->db->count_all_results("forum_topic_comments");
        return $result;
	}
	public function delete_forum_topic_child_comment($comment_id){
		$arr = array('parent_id' => $comment_id,'comment_account_id'=>config_item('id'));
		$this->db->where($arr);
		$this->db->update('forum_topic_comments',array('comment_deleted'=>1));
	}


	//New advanced queries

	public function list_forum($param = array()){
		$order_query = '';
		$limit_query = '';
		if(isset($param['order_by']) && isset($param['direction'])){
			$order_query = 'ORDER BY '.$param['order_by'].' '.$param['direction'];
		}

		if(isset($param['offset'])&&isset($param['limit'])){
			$limit_query = 'LIMIT '.$param['offset'].','.$param['limit'];		
		}
		$query = "SELECT forums.id,forums.forum_created,forums.forum_modified, forums.forum_name,forums.forum_slug,forums.forum_description,forum_topics_cp.total_topics, forum_topic_comments_cp.total_comments
			FROM forums 
			LEFT JOIN (SELECT forum_id, COUNT(id) as total_topics FROM forum_topics forum_topics_cp WHERE topic_deleted = '0' GROUP BY forum_id )  forum_topics_cp ON forums.id = forum_topics_cp.forum_id
			LEFT JOIN (SELECT forum_topics.forum_id, COUNT(forum_topic_comments_cp.id) as total_comments FROM forum_topic_comments forum_topic_comments_cp LEFT JOIN forum_topics ON forum_topic_comments_cp.topic_id = forum_topics.id WHERE parent_id = '0' AND comment_deleted = '0' GROUP BY forum_topics.forum_id ) forum_topic_comments_cp ON forums.id = forum_topic_comments_cp.forum_id WHERE forums.forum_account_id = '".config_item('id')."' AND forums.forum_deleted = '0' ".$order_query.' '.$limit_query;
			$return = $this->db->query($query)->result_array();
			return $return;
			//echo $this->db->last_query();die;
	}

	public function list_forum_topics($param = array()){
		$order_query = '';
		$limit_query = '';
		$forum_condition = '';
		$keyword_query = '';
		if(isset($param['order_by']) && isset($param['direction'])){
			$order_query = 'ORDER BY '.$param['order_by'].' '.$param['direction'];
		}

		if(isset($param['offset'])&&isset($param['limit'])){
			$limit_query = 'LIMIT '.$param['offset'].','.$param['limit'];		
		}

		if(isset($param['forum_id'])){
			$forum_condition = " AND forum_topics.forum_id =".$param['forum_id'];
		}

		if(isset($param['keyword'])&&$param['keyword']!=''){
			$keyword_query = ' topic_name LIKE "%'.$param['keyword'].'%" AND ';
		}
		$query = "SELECT forum_topics.*,forum_topic_comments_cp.total_comments,forum_topic_comments_cp.comment_created,forum_topic_comments_cp.user_id as comments_user_id FROM forum_topics LEFT JOIN (SELECT topic_id, COUNT(id) as total_comments,comment_created,user_id FROM forum_topic_comments forum_topic_comments_cp WHERE comment_deleted = 0 AND comment_blocked = 0 AND parent_id = 0 GROUP BY topic_id ORDER BY comment_created ASC) forum_topic_comments_cp ON forum_topics.id = forum_topic_comments_cp.topic_id WHERE ".$keyword_query." forum_topics.topic_deleted = 0 AND forum_topics.topic_account_id =".config_item('id').$forum_condition." ".$order_query." ".$limit_query;
			if(isset($param['count'])){
				$return = $this->db->query($query)->num_rows();
			}else{
				$return = $this->db->query($query)->result_array();
			}
			return $return;
			//echo $this->db->last_query();die;
	}

	public function get_forum_slug($forum_id){
		$this->db->select('forum_slug');
		$this->db->where('id',$forum_id);
		return $this->db->get('forums')->row_array();
	}

	function get_replies($param = array(),$parent_id){
        //$user               = $this->auth->get_current_user_session('user');
        if(isset($param['select'])){
            $this->db->select($param['select']);
        }else{
            $this->db->select('forum_topic_comments.id,forum_topic_comments.topic_comment,forum_topic_comments.user_id,forum_topic_comments.topic_id,forum_topic_comments.comment_created,forum_topic_comments.parent_id,forum_topic_comments.comment_account_id,users.us_name,users.us_image');
        }
        $this->db->from('forum_topic_comments');
        $this->db->join('users', 'forum_topic_comments.user_id = users.id','left');

        $cond = array('forum_topic_comments.comment_account_id'=>$this->config->item('id'),'forum_topic_comments.comment_deleted'=>0,'forum_topic_comments.comment_blocked'=>0);
        $this->db->where($cond);
        if(isset($parent_id)){
            $this->db->where('forum_topic_comments.parent_id',$parent_id);
        }
        if(isset($param['topic_id'])){
        	$this->db->where('topic_id',$param['topic_id']);
        }
        if(isset($param['comment_id'])&&$param['comment_id']!= ''&&$parent_id == 0){
            $this->db->where('forum_topic_comments.id',$param['comment_id']);
        }
        if($parent_id==0){
        	if(isset($param['order_by'])&&isset($param['direction'])){
	        	$this->db->order_by($param['order_by'],$param['direction']);
	        	$this->db->order_by('forum_topic_comments.comment_edited',$param['direction']);
	        }else{
	        	$this->db->order_by('forum_topic_comments.comment_created','DESC');
	        	$this->db->order_by('forum_topic_comments.comment_edited','DESC');
	        }
        }

        if($parent_id != 0){
        	if(isset($param['report_parent'])){
        		if($parent_id != $param['report_parent']){
	        		$this->db->limit($param['child_limit'],'0');
	        	}
	        	$this->db->order_by('forum_topic_comments.comment_created','DESC');
        	}else{
        		$this->db->limit($param['child_limit'],'0');
	            $this->db->order_by('forum_topic_comments.comment_created','DESC');
        	}
        }else{
            if(isset($param['limit'])&&isset($param['offset'])){
                $this->db->limit($param['limit'],$param['offset']);
            }
        }

        $discussion_tree = array();
        $discussions = $this->db->get()->result_array();

        if(!empty($discussions))
            {
                foreach ($discussions as $discussion) {
                    //$discussion['report_stat']    = $this->db_report_stat(array('comment_id'=>$discussion['id'],'user_id'=>$user['id']));
                    $discussion['children']       = $this->get_replies($param,$discussion['id']);
                    $discussion['children_count']       = $this->get_chiild_count($discussion['id']);
                    //$discussion_tree[$discussion['id']] = $discussion;
                    $discussion_tree[] = $discussion;
                }
            }
        //echo $this->db->last_query();die();
        return $discussion_tree;

    }
    function get_child_replies($param = array(),$parent_id){
        if(isset($param['select'])){
            $this->db->select($param['select']);
        }else{
            $this->db->select('forum_topic_comments.id,forum_topic_comments.topic_comment,forum_topic_comments.user_id,forum_topic_comments.topic_id,forum_topic_comments.comment_created,forum_topic_comments.parent_id,forum_topic_comments.comment_account_id,users.us_name,users.us_image');
        }
        $this->db->from('forum_topic_comments');
        $this->db->join('users', 'forum_topic_comments.user_id = users.id','left');
        $cond = array('forum_topic_comments.parent_id'=>$parent_id,'forum_topic_comments.comment_account_id'=>config_item('id'),'forum_topic_comments.comment_deleted'=>0,'forum_topic_comments.comment_blocked'=>0);
        $this->db->where($cond);
        if(isset($param['limit'])&&isset($param['offset'])){
            $this->db->limit($param['limit'],$param['offset']);
        }
        if(isset($param['order_by'])){
        	$this->db->order_by('forum_topic_comments.comment_created',$param['order_by']);
        }
        return $this->db->get()->result_array();
    }
    function db_comments_count($param = array()){
        $this->db->select('id');
        $cond = array('comment_deleted'=>0,'comment_blocked'=>0,'course_id'=>$param['c_id'],'parent_id'=>'0');
        $this->db->where($cond);
        return $this->db->get('course_discussions')->num_rows();
    }
    function get_chiild_count($parent_id){
        $this->db->select('id');
        $cond = array('parent_id'=>$parent_id,'comment_deleted'=>0,'comment_blocked'=>0);
        $this->db->where($cond);
        $return = $this->db->get('forum_topic_comments')->num_rows();
        //echo $this->db->last_query();die;
        return $return;
    }
}
?>
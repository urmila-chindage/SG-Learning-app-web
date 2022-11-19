<?php 
Class News_letter_model extends CI_Model
{	
    function __construct()
    {
        parent::__construct();
    }
    public function check_subscription($arg){
    	$this->db->where('course_id',$arg);
    	$this->db->where('n_account_id',$this->config->item('id'));
    	return (bool) $this->db->count_all_results('news_letter');
    }
    public function insert_course_with_list_id($arg){
    	return $this->db->insert('news_letter', $arg);
    }
    public function get_list_cred($id){
    	$this->db->where('course_id',$id);
    	$this->db->where('n_account_id',$this->config->item('id'));
    	return $this->db->get('news_letter')->row();
    }
    public function update_course_with_list_id($args){
    	$this->db->where('course_id',$args['course_id']);
    	$this->db->where('n_account_id',$this->config->item('id'));
    	if(isset($args['zoho'])){
    		$val = array('zoho'=>$args['zoho']);
    	}else{
   			$val = array('mailchimp'=>$args['mailchimp']);
    	}
    	return $this->db->update('news_letter',$val);
    }
    public function get_course_name($id) {
		
		$this->db->select('ct_name');
		$this->db->from('categories');
		$this->db->where('ct_account_id',$this->config->item('id'));
		$this->db->where('id', $id);
		return $this->db->get()->row('ct_name');
		
	}






    public function check_topic_slug($slug, $id=false)
	{
		if($id)
		{
			$this->db->where('id !=', $id);
		}
		$this->db->where('topic_slug', $slug);
		
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
		$this->db->where('forum_slug', $slug);
		
		return (bool) $this->db->count_all_results('forums');
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
    	$this->db->order_by('forum_created', 'DESC');
		return $this->db->get('forums')->result();
		
	}
	public function create_forum($args){
		return $this->db->insert('forums', $args);
		
	}
	public function count_forum_posts($forum_id) {
		
		$this->db->select('forum_topic_comments.id');
		$this->db->from('forum_topic_comments');
		$this->db->join('forum_topics', 'forum_topic_comments.topic_id = forum_topics.id');
		$this->db->where('forum_topics.forum_id', $forum_id);
		$this->db->group_by('forum_topic_comments.id');
		return count($this->db->get()->result());
		
	}
	public function get_forum_latest_topic($forum_id) {
		
		$this->db->from('forum_topics');
		$this->db->where('forum_id', $forum_id);
		$this->db->order_by('topic_created', 'DESC');
		$this->db->limit(1);
		return $this->db->get()->row();
		
	}
	
	public function get_forum_id_from_forum_slug($slug) {
		
		$this->db->select('id');
		$this->db->from('forums');
		$this->db->where('forum_slug', $slug);
		return $this->db->get()->row('id');
		
	}
	public function get_forum($forum_id) {
		
		$this->db->from('forums');
		$this->db->where('id', $forum_id);
		return $this->db->get()->row();
		
	}
	function forum_count()
    {
        $result = $this->db->count_all_results("forums");
        return $result;
    }    
	public function get_topic($topic_id) {
		
		$this->db->from('forum_topics');
		$this->db->where('id', $topic_id);
		return $this->db->get()->row();
		
	}
	public function get_forum_topics($forum_id,$limit,$offset) {
		$this->db->from('forum_topics');
		$this->db->limit($limit,$offset);
		$this->db->order_by('topic_created', 'DESC');
		$this->db->where('forum_id', $forum_id);
		return $this->db->get()->result();
		
	}
	public function topic_count($forum_id){
		$this->db->where('forum_id', $forum_id);
		$result = $this->db->count_all_results("forum_topics");
        return $result;
	}
	public function get_username_from_user_id($user_id) {
		
		$this->db->select('us_name');
		$this->db->from('users');
		$this->db->where('id', $user_id);

		return $this->db->get()->row('us_name');
		
	}
	public function get_posts($topic_id,$limit,$offset) {
		
		$this->db->from('forum_topic_comments');
		$this->db->limit($limit,$offset);
		$this->db->order_by('comment_created', 'DESC');
		$this->db->where('topic_id', $topic_id);
		$this->db->where('parent_id',0);
		return $this->db->get()->result();
		
	}
	public function post_count($topic_id){
		$this->db->where('topic_id', $topic_id);
		$this->db->where('parent_id', 0);
		$result = $this->db->count_all_results("forum_topic_comments");
        return $result;
	}
	public function get_topic_latest_post($topic_id) {
		
		$this->db->from('forum_topic_comments');
		$this->db->where('topic_id', $topic_id);
		$this->db->order_by('comment_created', 'DESC');
		$this->db->limit(1);
		return $this->db->get()->row();
		
	}
	public function create_topic($arg){
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
		);
		
		if ($this->db->insert('forum_topic_comments', $data)) {
			
			$data = array('topic_modified' => date('Y-m-j H:i:s'));
			$this->db->where('id', $topic_id);
			return $this->db->update('forum_topics', $data);
			
		}
		return false;
		
	}
	public function get_topic_id_from_topic_slug($topic_slug) {
		
		$this->db->select('id');
		$this->db->from('forum_topics');
		$this->db->where('topic_slug', $topic_slug);
		return $this->db->get()->row('id');
		
	}
	public function delete_forum_topic_comments($topic_id){
		$arr = array('topic_id' => $topic_id);
		$this->db->where($arr);
		$this->db->delete('forum_topic_comments');
	}
	public function delete_forum_topic_comment($comment_id){
		$arr = array('id' => $comment_id);
		$this->db->where($arr);
		$this->db->delete('forum_topic_comments');
	}
	public function delete_forum_topics($forum_id){
		$arr = array('forum_id' => $forum_id);
		$this->db->where($arr);
		$this->db->delete('forum_topics');
	}
	public function delete_forum_topic($topic_slug){
		$arr = array('topic_slug' => $topic_slug);
		$this->db->where($arr);
		$this->db->delete('forum_topics');
	}
	public function delete_forum($slug){
		$arr = array('forum_slug' => $slug);
		$this->db->where($arr);
		$this->db->delete('forums');
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

	//Search methods
	public function search_topic_count($forum_id,$search_term){
		$this->db->where('forum_id', $forum_id);
		$this->db->where("topic_name LIKE '%".$search_term."%'");
		$result = $this->db->count_all_results("forum_topics");
        return $result;
	}
	public function get_search_forum_topics($forum_id,$search_term,$limit,$offset) {
		$this->db->from('forum_topics');
		$this->db->limit($limit,$offset);
		$this->db->where("topic_name LIKE '%".$search_term."%'");
		$this->db->order_by('topic_created', 'DESC');
		$this->db->where('forum_id', $forum_id);
		return $this->db->get()->result();
		
	}
	public function get_child_comments($parent_id,$offset,$limit){
		$this->db->from('forum_topic_comments');
		$this->db->limit($limit,$offset);
		$this->db->order_by('comment_created', 'DESC');
		$this->db->where('parent_id', $parent_id);
		return $this->db->get()->result();
	}
	public function add_child_comment($arr){
		$this->db->insert('forum_topic_comments',$arr);
		$id = $this->db->insert_id();
		$q = $this->db->get_where('forum_topic_comments', array('id' => $id));
		return $q->row();
	}
	public function get_users_in_topic($topic_id){
		$this->db->select('user_id');
		$this->db->from('forum_topic_comments');
		$this->db->where('topic_id',$topic_id);
		return $this->db->get()->result();	
	}
	public function get_user_cred($user){
		$this->db->from('users');
		$this->db->where('id', $user);
		return $this->db->get()->row();	
	}
}
?>
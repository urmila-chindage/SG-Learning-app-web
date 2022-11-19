<?php
class Group_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }

  public function groups($param = array())
  {
    $limit        = isset($param['limit']) ? $param['limit'] : 0;
    $offset       = isset($param['offset']) ? $param['offset'] : 0;
    $order_by     = isset($param['order_by']) ? $param['order_by'] : 'id';
    $direction    = isset($param['direction']) ? $param['direction'] : 'DESC';
    $status       = isset($param['status']) ? $param['status'] : '';
    $count        = isset($param['count']) ? $param['count'] : false;
    $not_deleted  = isset($param['not_deleted']) ? $param['not_deleted'] : false;
    $keyword      = isset($param['keyword']) ? $param['keyword'] : '';
    $user_id      = isset($param['user_id']) ? $param['user_id'] : '';
    $select       = isset($param['select']) ? $param['select'] : '*';
    $role_id      = isset($param['role_id']) ? $param['role_id'] : '1';
    $institute_id = isset($param['institute_id']) ? $param['institute_id'] : '';
    $batch_id     = isset($param['batch_id']) ? $param['batch_id'] : false;

    $this->db->order_by($order_by, $direction);
    if ($limit > 0) {
    $this->db->limit($limit, $offset);
    }
    if ($keyword) {
      $this->db->group_start();
      $this->db->or_like(['gp_name' => $keyword, 'gp_year' => $keyword, 'gp_institute_code' => $keyword]);
      $this->db->group_end();
    }
    if ($not_deleted) {
    $this->db->where('gp_deleted', '0');
    }
    if ($status != '') {
    $this->db->where('gp_status', $status);
    }

    if ($institute_id != '') {
    $this->db->where('gp_institute_id', $institute_id);
    }
    if ($batch_id) {
    $this->db->where('id', $batch_id);
    }
    $this->db->where('gp_account_id', config_item('id'));
    $this->db->select($select);
    if ($count) {
    $result = $this->db->count_all_results('groups');
    } else {
    $result = $this->db->get('groups')->result_array();
    }
    // echo $this->db->last_query();die;
    return $result;
  }
  public function course_groups($param = array())
  {

    $course_id    = isset($param['course_id']) ? $param['course_id'] : false;
    $keyword      = isset($param['keyword']) ? $param['keyword'] : false;
    $institute_id = isset($param['institute_id']) ? $param['institute_id'] : false;
    $select       = isset($param['select']) ? $param['select'] : 'groups.*';
    $count        = isset($param['count']) ? $param['count'] : false;
    $not_deleted  = isset($param['not_deleted']) ? $param['not_deleted'] : false;
    $order_by     = isset($param['order_by']) ? $param['order_by'] : 'groups.gp_name';
    $direction    = isset($param['direction']) ? $param['direction'] : 'ASC';
    $limit        = isset($param['limit']) ? $param['limit'] : 0;
    $offset       = isset($param['offset']) ? $param['offset'] : 0;

    if (!$course_id) {
    return array();
    }
    $this->db->select($select);
    if ($institute_id) {
    $this->db->where('groups.gp_institute_id', $institute_id);
    }
    $this->db->where('course_basics.id', $course_id);
    if ($keyword) {
    $this->db->group_start();
    $this->db->or_like(['groups.gp_name' => $keyword, 'gp_year' => $keyword, 'gp_institute_code' => $keyword]);
    $this->db->group_end();
    }
    $this->db->where('groups.gp_account_id', config_item('id'));
    if ($not_deleted) {
      $this->db->where('groups.gp_deleted', '0');
    }
    $this->db->join('groups', 'FIND_IN_SET (groups.id, course_basics.cb_groups)', 'left');
    $this->db->order_by($order_by, $direction);
    if ($limit > 0) {
    $this->db->limit($limit, $offset);
    }
    $result = $this->db->get('course_basics')->result_array();
    // echo $this->db->last_query();die;
    if ($count) {
    return sizeof($result);
    } else {
    return $result;
    }
  }

  public function group($param = array())
  {
      $select  = (isset($param['select']) ? $param['select'] : '*');
      $deleted = isset($param['deleted'])?$param['deleted']:false;

      if (isset($param['name'])) {
        $this->db->where('gp_name', $param['name']);
      }
      if (isset($param['id'])) {
        $this->db->where('id', $param['id']);
      }
      if (isset($param['exclude_id'])) {
        $this->db->where('id!=', $param['exclude_id']);
      }
      if (isset($param['institute_id'])) {
        $this->db->where('gp_institute_id', $param['institute_id']);
      }
      if($deleted){
        $this->db->where('gp_deleted','0');
      }
    
    
      $this->db->select($select);
      $this->db->limit(1);
      $result = $this->db->get('groups');
      return $result->row_array();
  }

  public function course_groups_not_added($param = array())
  {
    $limit        = isset($param['limit']) ? $param['limit'] : 0;
    $offset       = isset($param['offset']) ? $param['offset'] : 0;
    $course_id    = isset($param['course_id']) ? $param['course_id'] : false;
    $keyword      = isset($param['keyword']) ? $param['keyword'] : false;
    $user_id      = isset($param['user_id']) ? $param['user_id'] : false;
    $not_deleted  = isset($param['not_deleted']) ? $param['not_deleted'] : false;
    $institute_id = isset($param['institute_id']) ? $param['institute_id'] : false;
    $select       = isset($param['select']) ? $param['select'] : '*';
    $count        = isset($param['count']) ? $param['count'] : false;

    if (!$course_id) {
    return array();
    }
    $group = $this->db->query('SELECT cb_groups FROM course_basics WHERE id=' . $course_id . '')->row_array();
    if (isset($group['cb_groups']) && $group['cb_groups'] != '') {
    $this->db->where_not_in('groups.id', ($group['cb_groups']), false);
    }

    if ($not_deleted) {
    $this->db->where('groups.gp_deleted', '0');
    }

    if ($limit > 0) {
    $this->db->limit($limit, $offset);
    }

    if ($keyword) {
    $this->db->like('groups.gp_name', $keyword);
    }

    if ($institute_id) {
    $this->db->where('groups.gp_institute_id', $institute_id);
    }

    $this->db->where('groups.gp_account_id', config_item('id'));
    $this->db->select($select);
    $result = $this->db->get('groups')->result_array();
    //echo $this->db->last_query();die;
    if ($count) {
    return sizeof($result);
    } else {
    return $result;
    }

  }
  /*
    Controller used : Groups
    edited          : none
  */
  public function group_users($param = array())
  {
    $group_id   = isset($param['group_id']) ? $param['group_id'] : false;
    $count      = isset($param['count']) ? $param['count'] : false;
    $limit      = isset($param['limit']) ? $param['limit'] : false;
    $offset     = isset($param['offset']) ? $param['offset'] : 0;
    $select     = isset($param['select']) ? $param['select'] : '*';
    $verified   = isset($param['verified']) ? $param['verified'] : false;
    if (!$group_id) 
    {
      return array();
    }
        
    $this->db->select($select);
    $this->db->like('concat(",",users.us_groups,",")', ',' . $group_id . ',');
    $this->db->where('users.us_account_id', config_item('id'));

    if ($verified) {
    $this->db->where('users.us_email_verified', '1');
    }
    if ($limit) {
    $this->db->limit($limit, $offset);
    }
    if ($count) {
    $result = $this->db->count_all_results('users');
    } else {
    $result = $this->db->get('users')->result_array();
    }
    //echo $this->db->last_query();die;
    return $result;
  }

 //Created By Yadu Chandran
 //Model for getting user email using group id
 public function group_email($param = array())
 {
  $group_id = isset($param['group_id']) ? $param['group_id'] : false;
  if (!$group_id) {
   return array();
  }

  $this->db->like('concat(",",users.us_groups,",")', ',' . $group_id . ',');
  $this->db->select('us_email');
  $this->db->from('users');
  $result = $this->db->get()->result_array();
  return $result;
 }

 public function save($data)
 {
  if ($data['id']) {
   $this->db->where('id', $data['id']);
   $this->db->update('groups', $data);
   return $data['id'];
  } else {
   $this->db->insert('groups', $data);
   return $this->db->insert_id();
  }
 }

 /*
  * Modified by Neethu
  * Modified date : 04/01/2017
  */

 public function group_members($param = array())
 {
  $group_id         = isset($param['group_id']) ? $param['group_id'] : false;
  $exclude_group_id = isset($param['exclude_group_id']) ? $param['exclude_group_id'] : false;
  $keyword          = isset($param['keyword']) ? $param['keyword'] : false;
  $count            = isset($param['count']) ? $param['count'] : false;
  $teacher_id       = isset($param['teacher_id']) ? $param['teacher_id'] : false;
  $not_deleted      = isset($param['not_deleted']) ? $param['not_deleted'] : false;

  $admin = $this->auth->get_current_user_session('admin');
  // $institute_filter   = (isset($admin['id'])&&$admin['us_role_id']==8)?$admin['us_institute_id']:0;

  if (isset($admin['id']) && $admin['us_role_id'] == 8) {
   $institute_filter = $admin['us_institute_id'];
  } else {
   $institute_filter = isset($param['institute_id']) ? $param['institute_id'] : '0';
  }

  $course_subscribers = 0;
  if ($teacher_id) {

   $this->db->select('GROUP_CONCAT(ct_course_id) as course_ids');
   $this->db->where('ct_tutor_id', $teacher_id);
   $tutor_courses = $this->db->get('course_tutors')->row_array();
   $tutor_courses = isset($tutor_courses['course_ids']) ? $tutor_courses['course_ids'] : 0;

   if ($tutor_courses > 0) {
    $this->db->select('GROUP_CONCAT(cs_user_id) as user_ids');
    $this->db->where_in('cs_course_id', explode(',', $tutor_courses));
    $course_subscribers = $this->db->get('course_subscription')->row_array();
    $course_subscribers = isset($course_subscribers['user_ids']) ? $course_subscribers['user_ids'] : 0;
   }
  }

  $this->db->select('id, us_name, us_email, us_image');
  if ($group_id) {
   $this->db->like('concat(",",users.us_groups,",")', ',' . $group_id . ',');
  }
  if ($exclude_group_id) {
   $this->db->not_like('concat(",",users.us_groups,",")', ',' . $exclude_group_id . ',');
  }
  if ($course_subscribers > 0) {
   $this->db->where_in('users.id', explode(',', $course_subscribers));
  }
  if ($not_deleted) {
   $this->db->where_in('users.us_deleted', '0');
  }
  if ($institute_filter != 0) {
   $this->db->where('users.us_institute_id', $institute_filter);
  }
  if ($keyword) {
   $this->db->group_start();
   $this->db->or_like(['us_name' => $keyword, 'us_email' => $keyword]);
   $this->db->group_end();
  }
  $this->db->where('users.us_role_id', '2');
  $this->db->where_not_in('users.id', array(config_item('super_admin')));
  $this->db->where('users.us_account_id', config_item('id'));
  if ($count) {
   $result = $this->db->count_all_results('users');
  } else {
   $this->db->group_by('users.id');
   $result = $this->db->get('users')->result_array();
  }
  // echo $this->db->last_query();die;
  return $result;
 }

 public function group_courses($param = array())
 {
  $group_id = isset($param['group_id']) ? $param['group_id'] : 0;
  $select   = isset($param['select']) ? $param['select'] : 'id';
  $this->db->select($select);
  $this->db->where('course_basics.cb_account_id', config_item('id'));
  $this->db->like('CONCAT(",", cb_groups, ",")', ',' . $group_id . ',');
  $return = $this->db->get('course_basics')->result_array();
  //echo $this->db->last_query();die;
  return $return;
 }

 function check_override_batch($group_id){
    $this->db->select('lecture_override.id,lecture_override.lo_override_batches,lecture_override.lo_lecture_type');
    $where = "FIND_IN_SET('".$group_id."', lecture_override.lo_override_batches)";  
    $this->db->where($where);
    $result                 = $this->db->get('lecture_override')->result_array();
    //echo $this->db->last_query();die;
    return $result;
  }

  function remove_user_from_group($users_ids = array(), $group_id = 0)
  {
      return $this->db->query("UPDATE users SET us_groups = TRIM(BOTH ',' FROM REPLACE(CONCAT(',', us_groups, ','), ',".$group_id.",', ',')) WHERE id IN (".implode($users_ids, ',').")");
  }

  public function update_institute_code($data = array())
  {
    $gp_institute_code  = isset($data['gp_institute_code']) ? $data['gp_institute_code'] : 0;
    $gp_institute_id    = isset($data['gp_institute_id']) ? $data['gp_institute_id'] : 0;
    if($gp_institute_id && $gp_institute_code)
    {
      $this->db->where('gp_institute_id', $gp_institute_id);
      $this->db->update('groups', $data); echo $this->db->last_query();
      return $this->db->affected_rows();
    }
  }

}

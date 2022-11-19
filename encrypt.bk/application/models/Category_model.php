<?php
class Category_model extends CI_Model
{
 public function __construct()
 {
  parent::__construct();
 }

 public function tutor_categories()
 {
  $query = "SELECT * FROM `categories` WHERE id IN (SELECT `cb_category` FROM `course_basics` WHERE cb_account_id='" . config_item('id') . "' AND id IN (SELECT ct_course_id FROM course_tutors LEFT JOIN users ON course_tutors.ct_tutor_id = users.id WHERE users.us_deleted = '0' AND users.us_status='1' AND us_role_id = 3 AND users.us_account_id='" . config_item('id') . "' GROUP BY ct_course_id) GROUP BY cb_category )";
  return $this->db->query($query)->result_array();
 }

 public function mentor_categories()
 {
  $query = "SELECT * FROM categories WHERE FIND_IN_SET(id, (SELECT TRIM(TRAILING ',' FROM GROUP_CONCAT(us_category_id))  FROM users WHERE users.us_deleted = '0' AND users.us_status='1' AND us_role_id = 6 AND users.us_account_id='" . config_item('id') . "'))";
  return $this->db->query($query)->result_array();
 }

 public function categories($param = array())
 {
      $limit        = isset($param['limit']) ? $param['limit'] : 0;
      $offset       = isset($param['offset']) ? $param['offset'] : 0;
      $order_by     = isset($param['order_by']) ? $param['order_by'] : 'ct_order';
      $direction    = isset($param['direction']) ? $param['direction'] : 'DESC';
      $status       = isset($param['status']) ? $param['status'] : 0;
      $name         = isset($param['name']) ? $param['name'] : '';
      $parent_id    = isset($param['parent_id']) ? $param['parent_id'] : '';
      $not_deleted  = isset($param['not_deleted']) ? $param['not_deleted'] : false;
      $not_category = isset($param['not_category']) ? $param['not_category'] : false;
      $count        = isset($param['count']) ? $param['count'] : false;
      $ids          = isset($param['ids']) ? $param['ids'] : array();
      $select       = isset($param['select'])?$param['select']:'categories.*';
      $key_word     = isset($param['search_keyword'])?$param['search_keyword']:'';

      $this->db->select($select);

      if (!empty($ids)) {
        $this->db->where_in('categories.id', $ids);
      }

      $this->db->order_by($order_by, $direction);
      if ($limit > 0) {
        $this->db->limit($limit, $offset);
      }
      if ($key_word) {
        $this->db->like('ct_name', $key_word);
       }
      if ($status) {
        $this->db->where('ct_status', '1');
      }
      if ($name) {
        $this->db->like('ct_name', $name);
      }

      if ($not_category) {
        $this->db->where('id != ', $not_category);
      }

      if ($not_deleted) {
        $this->db->where('ct_deleted', '0');
      }

      if ($parent_id != '') {
        $this->db->where('ct_parent_id', $parent_id);
      }

      $this->db->where('categories.ct_account_id', config_item('id'));
      if ($count) {
        $result = $this->db->count_all_results('categories');
      } else {
        $result = $this->db->get('categories')->result_array();
      }
      //echo $this->db->last_query();exit;
      return $result;
 }

 public function course_categories($param = array())
 {
      $limit        = isset($param['limit']) ? $param['limit'] : 0;
      $offset       = isset($param['offset']) ? $param['offset'] : 0;
      $order_by     = isset($param['order_by']) ? $param['order_by'] : 'ct_order';
      $direction    = isset($param['direction']) ? $param['direction'] : 'DESC';
      $name         = isset($param['name']) ? $param['name'] : '';
      $parent_id    = isset($param['parent_id']) ? $param['parent_id'] : '';
      $not_deleted  = isset($param['not_deleted']) ? $param['not_deleted'] : false;
      $not_category = isset($param['not_category']) ? $param['not_category'] : false;
      $count        = isset($param['count']) ? $param['count'] : false;
      $ids          = isset($param['ids']) ? $param['ids'] : array();
      $select       = isset($param['select'])?$param['select']:'categories.*';
      $key_word     = isset($param['search_keyword'])?$param['search_keyword']:'';

      $this->db->select($select);

      if (!empty($ids)) {
        $this->db->where_in('categories.id', $ids);
      }

      $this->db->order_by($order_by, $direction);
      if ($limit > 0) {
        $this->db->limit($limit, $offset);
      }
      if ($key_word) {
        $this->db->like('ct_name', $key_word);
       }
      if ($name) {
        $this->db->like('ct_name', $name);
      }

      if ($not_category) {
        $this->db->where('id != ', $not_category);
      }

      if ($not_deleted) {
        $this->db->where('ct_deleted', '0');
      }

      if ($parent_id != '') {
        $this->db->where('ct_parent_id', $parent_id);
      }

      $this->db->where('categories.ct_account_id', config_item('id'));
      if ($count) {
        $result = $this->db->count_all_results('categories');
      } else {
        $result = $this->db->get('categories')->result_array();
      }
      //echo $this->db->last_query();exit;
      return $result;
 }

 public function category($param = array())
 {
  //echo '<pre>'; print_r($param);die;
  $select = isset($param['select']) ? $param['select'] : 'categories.*';
  $ct_deleted = isset($param['not_deleted']) ? $param['not_deleted'] : false;
  $this->db->select($select);
  if (isset($param['status'])) {
   $this->db->where('categories.ct_status', 1);
  }
  if ($ct_deleted) {
    $this->db->where('ct_deleted', '0');
  }
  if (isset($param['category_name'])) {
   $this->db->where('categories.ct_name', $param['category_name']);
  }

  if (isset($param['except_id'])) {
   $this->db->where('categories.id!=', $param['except_id']);
  }

  if (isset($param['id'])) {
   $this->db->where('categories.id', $param['id']);
  }
  $this->db->where('categories.ct_account_id', config_item('id'));
  if (isset($param['count']) && $param['count'] == true) {
   return $this->db->count_all_results('categories');
  } else {
   $return = $this->db->get('categories')->row_array();
   //echo $this->db->last_query();die;
   return $return;
  }
 }

 public function question_category($param = array())
 {
//        $this->db->select('questions_category.*,categories.ct_name');
  //        $this->db->join('categories','categories.id = questions_category.qc_parent_id');
  if (isset($param['status'])) {
   $this->db->where('qc_status', 1);
  }
  if (isset($param['category_name'])) {
   $this->db->where('qc_category_name', $param['category_name']);
  }
  if (isset($param['parent_category'])) {
   $this->db->where('qc_parent_id', $param['parent_category']);
  }
  if (isset($param['id'])) {
   $this->db->where('id', $param['id']);
  }
  if (isset($param['count']) && $param['count'] == true) {
   return $this->db->count_all_results('questions_category');
  } else {
   $return = $this->db->get('questions_category')->row_array();
   return $return;
  }
 }

 public function save($data)
 {
    // print_r($data);die;
    if ($data['id']) {
    $this->db->where('id', $data['id']);
    $this->db->update('categories', $data);
    return $data['id'];
    } else {
    $this->db->insert('categories', $data);
    return $this->db->insert_id();
    }
 }

 public function save_question_category($data)
 {
  if ($data['id']) {
   $this->db->where('id', $data['id']);
   $this->db->update('questions_category', $data);
   return $data['id'];
  } else {
    $this->db->insert('questions_category', $data);
   return $this->db->insert_id();
  }
 }

 public function delete($id, $confirm_delete = false)
 {
  if ($confirm_delete) {
   $this->db->where('id', $id);
   $this->db->delete('categories');
  } else {
   $save               = array();
   $save['id']         = $id;
   $save['ct_deleted'] = 1;
   $this->save($save);
  }
 }

 public function get_question_category($param = array())
 {
  $cat_id     = isset($param['cat_id']) ? $param['cat_id'] : false;
  $difficulty = isset($param['difficulty']) ? $param['difficulty'] : false;
  $methods    = isset($param['method']) ? $param['method'] : false;
  $this->db->select('qc_category_name, COUNT(*) as qc_category_count, questions_category.id');
  $this->db->from('questions_category');
  $this->db->join('questions', 'questions.q_category = questions_category.id');
  $this->db->join('course_basics', 'course_basics.id = questions.q_course_id');
  $this->db->group_by('questions_category.id');
  if ($cat_id) {
   $this->db->where('course_basics.cb_category', $cat_id);
  }

  if ($difficulty) {
   $this->db->where('questions.q_difficulty', $difficulty);
  }
  $result = $this->db->get()->result_array();
  return $result;
 }

 public function question_categories($param = array())
 {
  $limit       = isset($param['limit']) ? $param['limit'] : 0;
  $offset      = isset($param['offset']) ? $param['offset'] : 0;
  $order_by    = isset($param['order_by']) ? $param['order_by'] : 'id';
  $direction   = isset($param['direction']) ? $param['direction'] : 'DESC';
  $status      = isset($param['status']) ? $param['status'] : 0;
  $name        = isset($param['name']) ? $param['name'] : false;
  $parent_id   = isset($param['parent_id']) ? $param['parent_id'] : false;
  $not_deleted = isset($param['not_deleted']) ? $param['not_deleted'] : false;

  $this->db->select('questions_category.*,categories.ct_name');
  $this->db->join('categories', 'categories.id = questions_category.qc_parent_id');
  $this->db->order_by($order_by, $direction);
  if ($limit > 0) {
   $this->db->limit($limit, $offset);
  }
  if ($parent_id) {
   $this->db->where('qc_parent_id', $parent_id);
  }
  if ($status) {
   $this->db->where('qc_status', '1');
  }
  if ($not_deleted) {
   $this->db->where('qc_deleted', '0');
  }
  if ($name) {
   $this->db->like('qc_category_name', $name);
  }
  $this->db->where('questions_category.qc_account_id', config_item('id'));
  $result = $this->db->get('questions_category');
  //echo $this->db->last_query();die;
  return $result->result_array();
 }

 public function get_categories_with_questions($param = array())
 {
  $result      = array();
  $category_id = isset($param['category_id']) ? $param['category_id'] : false;
  if ($category_id) {
   $query = 'SELECT questions_category.*
                        FROM questions
                        LEFT JOIN questions_category ON questions.q_category = questions_category.id
                        WHERE q_category IN (SELECT id FROM questions_category WHERE qc_parent_id = ' . $category_id . '  )
                        GROUP BY q_category';
   $result = $this->db->query($query)->result_array();
  }
  return $result;
 }

 public function ques_categories($param = array())
 {

  $sql = "SELECT id,ct_name FROM categories WHERE id IN(SELECT DISTINCT(qc_parent_id) AS id FROM questions_category WHERE qc_account_id = '" . $this->config->item('id') . "' AND qc_status = '1' AND qc_deleted = '0') AND ct_deleted = '0' AND ct_status = '1' AND ct_account_id = '" . $this->config->item('id') . "'";
  return $this->db->query($sql)->result_array();
 }

 public function ques_count($param = array())
 {

  $sql = "SELECT * FROM questions WHERE q_category ='" . $param['topic_id'] . "'";
  return $this->db->query($sql)->num_rows();
 }

 public function migrate_ques_cat($param = array())
 {
  $this->db->where('q_category', $param['from_cat']);
  return $this->db->update('questions', array('q_category' => $param['to_cat']));
 }

 //for tags
 public function tags($param = array())
 {
  if (isset($param['ids'])) {
   $this->db->where_in('id', explode(',', $param['ids']));
  }
  $this->db->where('tg_account_id', config_item('id'));
  if (isset($param['count']) && $param['count'] == true) {
   return $this->db->count_all_results('tags');
  } else {
   $return = $this->db->get('tags')->result_array();
   return $return;
  }
 }
 public function tag($param = array())
 {
  if (isset($param['tag_name'])) {
   $this->db->where('tg_name', $param['tag_name']);
  }
  if (isset($param['id'])) {
   $this->db->where('id', $param['id']);
  }
  $this->db->where('tg_account_id', config_item('id'));
  $return = $this->db->get('tags')->row_array();
  return $return;
 }

 public function save_tag($data)
 {
  // print_r($data);die;
  if ($data['id']) {
   $this->db->where('id', $data['id']);
   $this->db->update('tags', $data);
   return $data['id'];
  } else {
   $this->db->insert('tags', $data);
   return $this->db->insert_id();
  }
 }
 //end

 //for subject
 public function subjects($param = array())
 {
  $select = 'questions_subject.*';
  if (isset($param['select'])) {
   $select = $param['select'];
  }
  $this->db->select($select);
  $qs_deleted = isset($param['qs_deleted']) ? $param['qs_deleted'] : false;
  $order_by   = isset($param['order_by']) ? $param['order_by'] : 'id';
  $direction  = isset($param['direction']) ? $param['direction'] : 'DESC';
  if (isset($param['subject_name'])) {
   $this->db->like('qs_subject_name', $param['subject_name']);
  }
  if (isset($param['category_id'])) {
   $this->db->where('qs_category_id', $param['category_id']);
  }
  if ($qs_deleted) {
   $this->db->where('qs_deleted', '0');
  }

  $this->db->where('qs_account_id', config_item('id'));

  $this->db->order_by($order_by, $direction);
  if (isset($param['count']) && $param['count'] == true) {
   return $this->db->count_all_results('questions_subject');
  } else {
   $return = $this->db->get('questions_subject')->result_array();
   //echo $this->db->last_query();die;
   return $return;
  }
 }

 public function subject($param = array())
 {
  $qs_deleted = isset($param['qs_deleted']) ? $param['qs_deleted'] : false;
    if (isset($param['subject_name'])) {
    $this->db->where('qs_subject_name', $param['subject_name']);
    }
    if (isset($param['id'])) {
    $this->db->where('id', $param['id']);
    }
    if (isset($param['category_id'])) {
      $this->db->where('qs_category_id', $param['category_id']);
    }
    if (isset($param['subject_id'])) {
      if(isset($param['excludes']) && $param['excludes']){ 
        $this->db->where_not_in('id', array($param['subject_id']));
        //$this->db->where('qs_category_id', $param['category_id']);
      }
    }

  if ($qs_deleted) {
    $this->db->where('qs_deleted', '0');
   }
  $this->db->where('qs_account_id', config_item('id'));
  if (isset($param['count']) && $param['count'] == true) {
   return $this->db->count_all_results('questions_subject');
  } else {
   $return = $this->db->get('questions_subject')->row_array();
   return $return;
  }
  
 }
 public function save_subject($data)
 {
  // print_r($data);die;
  if ($data['id']) {
   $this->db->where('id', $data['id']);
   $this->db->update('questions_subject', $data);
   return $data['id'];
  } else {
   $this->db->insert('questions_subject', $data);
   return $this->db->insert_id();
  }
 }
 //End

 //for subject
 public function topics($param = array())
 {
  $select = 'questions_topic.*';
  if (isset($param['select'])) {
   $select = $param['select'];
  }
  $this->db->select($select);
  $qt_deleted = isset($param['qt_deleted']) ? $param['qt_deleted'] : false;
  $order_by   = isset($param['order_by']) ? $param['order_by'] : 'id';
  $direction  = isset($param['direction']) ? $param['direction'] : 'DESC';
  if (isset($param['topic_name'])) {
   $this->db->like('qt_topic_name', $param['topic_name']);
  }

  if (isset($param['category_id'])) {
   $this->db->where('qt_category_id', $param['category_id']);
  }
  if (isset($param['subject_id'])) {
   $this->db->where('qt_subject_id', $param['subject_id']);
  }
  if ($qt_deleted) {
   $this->db->where('qt_deleted', '0');
  }
  $this->db->where('qt_account_id', config_item('id'));
  $this->db->order_by($order_by, $direction);
  if (isset($param['count']) && $param['count'] == true) {
   return $this->db->count_all_results('questions_topic');
  } else {
   $return = $this->db->get('questions_topic')->result_array();
   return $return;
  }
  // echo $this->db->last_query();die;
 }

 public function topic($param = array())
 {
  $qt_deleted = isset($param['qt_deleted']) ? $param['qt_deleted'] : false;
  if (isset($param['topic_name'])) {
   $this->db->where('qt_topic_name', $param['topic_name']);
  }
  if (isset($param['id'])) {
    
      $this->db->where('id', $param['id']);
  }

  if (isset($param['excludes'])) {
    $this->db->where_not_in('id', $param['excludes']);
  }
  
  if (isset($param['category_id'])) {
   $this->db->where('qt_category_id', $param['category_id']);
  }
  if (isset($param['subject_id'])) {
   $this->db->where('qt_subject_id', $param['subject_id']);
  }
  if ($qt_deleted) {
  $this->db->where('qt_deleted', '0');
  }
  $this->db->where('qt_account_id', config_item('id'));
  if (isset($param['count']) && $param['count'] == true) {
   return $this->db->count_all_results('questions_topic');
  } else {
   $return = $this->db->get('questions_topic')->row_array();
   return $return;
  }
  //echo $this->db->last_query();die;
 }
 public function save_topic($data)
 {
  if ($data['id']) {
   $this->db->where('id', $data['id']);
   $this->db->update('questions_topic', $data);
   return $data['id'];
  } else {
   $this->db->insert('questions_topic', $data);
   return $this->db->insert_id();
  }
  //echo $this->db->last_query();die;
 }

 public function merge_subject($data)
 {
  $merge_set_one = array('qs_deleted' => '1');
  $this->db->where_in('id', $data['merge_subject_ids']);
  $this->db->update('questions_subject', $merge_set_one);
  //echo $this->db->last_query();die;
  $merge_set_two = array('qt_subject_id' => $data['subject_id']);
  $this->db->where_in('qt_subject_id', $data['merge_subject_ids']);
  $this->db->update('questions_topic', $merge_set_two);

  $merge_set_three = array('q_subject' => $data['subject_id']);
  $this->db->where_in('q_subject', $data['merge_subject_ids']);
  $this->db->update('questions', $merge_set_three);
  return $data['subject_id'];

 }

 public function merge_topic($data)
 {
  $merge_set_one = array('qt_deleted' => '1');
  $this->db->where_in('id', $data['merge_topic_ids']);
  $this->db->update('questions_topic', $merge_set_one);

  $merge_set_two = array('q_topic' => $data['topic_id']);
  $this->db->where_in('q_topic', $data['merge_topic_ids']);
  $this->db->update('questions', $merge_set_two);
  return $data['topic_id'];

 }

 //End

 public function save_bulk($categories = array())
 {
    if(!empty($categories))
    {
        $this->db->trans_start();
        foreach ($categories as $order => $id) 
        {
          $save                   = array();
          //$save['id']             = $id;
          $save['ct_order']       = ($order)+1;
          $this->db->where('id', $id);
          $this->db->update('categories', $save);
        }
        $this->db->trans_complete(); 
    }
 }
}

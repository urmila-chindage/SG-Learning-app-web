<?php 
Class Generate_test_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    function get_questions($category_id, $number){

        $this->db->select('*');
        $this->db->from('questions');
        $this->db->where('q_category', $category_id);
        $this->db->order_by('RAND()');
        $this->db->limit($number);
        return $this->db->get()->result_array();
    }
        
    function questions($param=array())
    { 
        $not_deleted    = isset($param['not_deleted'])?$param['not_deleted']:false;
        $direction      = isset($param['direction'])?$param['direction']:'DESC';
        $order_by       = isset($param['order_by'])?$param['order_by']:'id';
        $keyword        = isset($param['keyword'])?$param['keyword']:'';
        $filter         = isset($param['filter'])?$param['filter']:0;
        $type           = isset($param['type'])?$param['type']:0;
        $question_types = array('single' => '1', 'multiple' => '2', 'subjective' => '3', 'blanks' => '4');
        $next_id_of     = isset($param['next_id_of'])?$param['next_id_of']:false;
        
        
        $limit          = isset($param['limit'])?$param['limit']:0;
        $offset         = isset($param['offset'])?$param['offset']:0;
        $count          = isset($param['count'])?$param['count']:false;

        $category_id    = isset($param['category_id'])?$param['category_id']:'';
        $subject_id     = isset($param['subject_id'])?$param['subject_id']:'';
        $topic_id       = isset($param['topic_id'])?$param['topic_id']:'';
                
        $this->db->select('questions.id,questions.q_category,questions.q_type,questions.q_question,questions_category.qc_parent_id as questions_parent_id,questions.q_code,questions.q_pending_status');
        $this->db->join('questions_category', 'questions.q_category = questions_category.id', 'left');
        
    
        
        if( $keyword )
        {    
        $this->db->group_start();
        $this->db->or_like([  'questions.id' => trim($keyword),'questions.q_code' => trim($keyword) , 'CONCAT(",", questions.q_tags_label, ",")' => trim(','.$keyword.',') ]);
        $this->db->group_end();
            //$this->db->like('questions.q_question', $keyword); 
           // $this->db->where('(`questions.q_question` LIKE \'%'.$keyword.'%\' OR `questions.id` LIKE \'%'.$keyword.'%\')', NULL, FALSE);
            //$this->db->or_like('questions.id', $keyword); 
        }
        if( $not_deleted )
        {
            $this->db->where('questions.q_deleted', '0');
        }
        if( isset($question_types[$type]) )
        {
            $this->db->like('q_type', $question_types[$type]); 
        }
        
        if( $category_id != 'all' && $category_id != '' )
        {
            $this->db->where('questions.q_category', $category_id); 
        }
        if( $subject_id != 'all' && $subject_id != '' )
        {
            $this->db->where('questions.q_subject', $subject_id); 
        }        
        if( $topic_id != 'all' && $topic_id != '' )
        {
            $this->db->where('questions.q_topic', $topic_id); 
        }
        $this->db->where("(questions.q_account_id = '0' OR questions.q_account_id='".config_item('id')."')");
        
        if( $filter )
        {
            switch ($filter) {
                case 'easy':
                    $status = '1';
                    $this->db->where('q_difficulty', '1'); 
                    break;
                case 'medium':
                    $this->db->where('q_difficulty', '2'); 
                    $status = '0';
                    break;
                case 'hard':
                    $this->db->where('q_difficulty', '3'); 
                    $status = '2';
                    break;

                default:
                    break;
            }
        }
        if($next_id_of)
        {
            $this->db->where('questions.id < ', $next_id_of); 
        }
        if($limit)
        {
            $this->db->limit($limit);
            $this->db->offset($offset);
        }
        $this->db->order_by($order_by, $direction);
        if($count)
        {
            $result =  $this->db->count_all_results('questions');   
        }
        else
        {
            $result =  $this->db->get('questions')->result_array();         
        }
        //echo $this->db->last_query();die;
        return $result;
    }
    
    function question($param=array())
    {
        if( isset($param['status'])) 
    {
            $this->db->where('q_status', '1');
    }
    if( isset($param['id'])) 
    {
            $this->db->where('id', $param['id']);
    }
        $this->db->where('questions.q_account_id', config_item('id'));
    $result = $this->db->get('questions')->row_array(); 
//echo $this->db->last_query();die;
        return $result;
    }
    
    function save_generate_test_question($data)
    {
    if($data['id'])
    {
            $this->db->where('id', $data['id']);
            $this->db->update('questions', $data);
            return $data['id'];
        }
    else
    {
            $this->db->insert('questions', $data);
            return $this->db->insert_id();
    }
    }
    
    function delete_question($question_id)
    {
        //finally update delete status of question
        
        $data=array('q_deleted'=>'1');
        
        $this->db->where('id', $question_id);
        $this->db->update('questions', $data);
        //end
        return true;
    }

    function delete_question_bulk($question_ids)
    {
        $questions_chunks  = array_chunk($question_ids, 50);
        $status            = 1;
        if(!empty($questions_chunks))
        {
            foreach($questions_chunks as $questions)
            {
                $this->db->trans_start();
                foreach($questions as $q_id)
                {
                    $this->db->query("UPDATE questions SET q_deleted = '".$status."' WHERE id = '".$q_id."';");
                }
                $this->db->trans_complete(); 
            }
        }
    }
    
    function options($param=array())
    {
        $q_options = isset($param['q_options'])?$param['q_options']:false;
        $q_answer = isset($param['q_answer'])?$param['q_answer']:false;
        if( $q_options ) 
    {
            $this->db->where_in('id', array_map('intval', explode(',', $q_options)));
            $result = $this->db->get('questions_options')->result_array();  
            return $result;
    }
        if( $q_answer ) 
    {
            $this->db->where_in('id', array_map('intval', explode(',', $q_answer)));
            $result = $this->db->get('questions_options')->result_array();  
            return $result;
    }
        //echo $this->db->last_query();die;
    }
    
    function save_option($data)
    {
    if($data['id'])
    {
            $this->db->where('id', $data['id']);
            $this->db->update('questions_options', $data);
            return $data['id'];
        }
    else
    {
            $this->db->insert('questions_options', $data);
            return $this->db->insert_id();
    }
    }
    
    function delete_option($option_id)
    {
        $this->db->where('id', $option_id);     
        $this->db->delete('questions_options');
    }

    function generate_assesment($save){

        $this->db->insert('user_generated_assesment', $save);
        return $this->db->insert_id();
    }

    function save_question($attempt_id, $question_id){

        $save = array();
        $save['uga_assesment_id'] =  $attempt_id;
        $save['uga_question_id']  =  $question_id;
        $this->db->insert('user_generated_assesment_question', $save);
    }
        
        function generate_questions($param=array())
        {
            $this->db->select('id');
            $limit          = isset($param['limit'])?$param['limit']:0;
            $mode           = isset($param['mode'])?$param['mode']:false;
            $category_ids   = isset($param['category_ids'])?$param['category_ids']:array();
            if($mode)
            {
                $this->db->where('q_difficulty', $mode);
            }
            if(!empty($category_ids))
            {
                $this->db->where_in('q_category', $category_ids);
            }
            $this->db->order_by('RAND()');
            if($limit)
            {
                $this->db->limit($limit);
            }
            return $this->db->get('questions')->result_array();
        }
    
    function delete_topic($topic_id, $data)
    {
        $this->db->where('id', $topic_id);
        $this->db->update('questions_topic', $data);
    }

    function delete_subject($subject_id, $data)
    {
        $this->db->where('id',$subject_id);
        $this->db->update('questions_subject', $data);
        if(isset($subject_id))
        {
            $this->db->where('sr_subject_id',$subject_id);
            $this->db->delete('subject_report');
        }
    }


    function insert_options_bulk($question_options)
    {
        $return = array();
        $this->db->trans_start();
        foreach($question_options as $unique_hash => $options)
        {
            $return[$unique_hash] = array();
            foreach($options as $option)
            {
                $this->db->insert('options_tabke', $option);
                $return[$unique_hash][] = $this->db->insert_id();
            }
        }
        $this->db->trans_stop();
    }
}
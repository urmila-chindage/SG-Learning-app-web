<?php
class Test_result_model extends CI_Model
 {

    function get_total_correct_questions($assesment_id)
    {
        $query = $this->db->query('SELECT assessment_report.ar_question_id, questions.q_question, COUNT(assessment_report.ar_question_id) as total_correct
            FROM `assessment_report`
            LEFT JOIN questions ON assessment_report.ar_question_id = questions.id
            WHERE ar_question_id IN (SELECT aq_question_id FROM `assessment_questions` WHERE `aq_assesment_id` = "'.$assesment_id.'") AND assessment_report.ar_mark = questions.q_positive_mark AND assessment_report.ar_attempt_id IN (SELECT id  FROM `assessment_attempts` WHERE `aa_assessment_id` = "'.$assesment_id.'") GROUP BY assessment_report.ar_question_id');

        $result =  $query->result_array();
        return  $result;
    }
    
    function get_total_users($assesment_id)
    {
        $query = $this->db->query('SELECT count(*) as total_attempts FROM assessment_attempts WHERE aa_assessment_id = "'.$assesment_id.'"');

        $result =  $query->result();
        return  $result;
    }



    function get_assesment($param=array())
    {
        $assesment_id = isset($param['assesment_id'])?$param['assesment_id']:false;
        $this->db->select('assessments.id as assesment_id, assessments.a_duration as duration, assessments.a_lecture_id as lecture_id,  course_lectures.cl_lecture_name as assesment_name, course_lectures.cl_lecture_description, course_lectures.cl_lecture_type');
        $this->db->from('assessments');
        $this->db->join('course_lectures', 'course_lectures.id = assessments.a_lecture_id', 'left'); 
        if($assesment_id)
        {
            $this->db->where('assessments.id', $assesment_id);
        }
        $result =  $this->db->get()->row_array();
        return $result;
    }

    // function get_assesment_questions($param=array())
    // {
    //     $assesment_id = isset($param['assesment_id'])?$param['assesment_id']:false;
    //     $this->db->select('questions.id as question_id, assessment_questions.aq_assesment_id, questions.q_course_id, questions.q_type , questions.q_positive_mark, questions.q_negative_mark, questions.q_answer ');
    //     $this->db->from('assessment_questions');
    //     $this->db->join('questions', 'questions.id = assessment_questions.aq_question_id', 'left'); 
    //     if($assesment_id)
    //     {
    //         $this->db->where('assessment_questions.aq_assesment_id', $assesment_id);
    //     }
    //      $where = '(questions.q_type = 1 or questions.q_type = 2)';
    //      $this->db->where('questions.q_deleted', '0');
    //      $this->db->where( $where ); 
    //     $result =  $this->db->get()->result_array(); 
    //     return $result;  
    //    // echo $this->db->last_query();die; 
    // }
         
    // function get_answers($param=array())   
    // {
    //     //echo '<pre>'; print_r($param); die;
    //     $assesment_id = isset($param['assesment_id'])?$param['assesment_id']:false;
    //     $question_id  = isset($param['question_id'])?$param['question_id']:false;
    //     $answer_id    = isset($param['answer'])?$param['answer']:false;
    //     $type         = isset($param['type'])?$param['type']:false;

    //     $this->db->select('assessment_report.*');          
    //     $this->db->from('assessment_report');
    //   // $this->db->join('questions', 'questions.id = assessment_questions.aq_question_id', 'left'); 
    //     if($question_id)
    //     {
    //         $this->db->where('assessment_report.ar_question_id', $question_id);
    //     } 

    //     if($answer_id )
    //     {
    //         $this->db->where('assessment_report.ar_answer', $answer_id);
    //     }
              
    //    $result =  $this->db->get()->result_array(); 
    //    // $total_answers  = $result->num_rows();
    //    // echo $total_ansers; die; 
    //   //  echo '<pre>'; print_r($result); die;
    //   return $result; 
    //    // echo $this->db->last_query();die; 
    // }

 }

?>
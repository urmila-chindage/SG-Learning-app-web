<?php
 class Backup_model extends CI_Model
 {
    function __construct()
    {
        parent::__construct();
    }

    public function course($param = array())
    {
        $course_id = isset($param['course_id'])?$param['course_id']:0;
        $this->db->where('id', $course_id);
        return $this->db->get('course_basics')->row_array();
    }
    
    public function course_lectures($param = array())
    {
        $course_id              = isset($param['course_id'])?$param['course_id']:false;

        $cl_lecture_names       = isset($param['cl_lecture_names'])?$param['cl_lecture_names']:false;
        $cl_course_ids          = isset($param['cl_course_ids'])?$param['cl_course_ids']:false;
        $cl_section_ids         = isset($param['cl_section_ids'])?$param['cl_section_ids']:false;

        if($cl_lecture_names)
        {
            $this->db->where_in('cl_lecture_name', $cl_lecture_names);
        }

        if($cl_course_ids)
        {
            $this->db->where_in('cl_course_id', $cl_course_ids);
        }

        if($cl_section_ids)
        {
            $this->db->where_in('cl_section_id', $cl_section_ids);
        }

        if($course_id)
        {
            $this->db->where('cl_course_id', $course_id);
        }
        //$this->db->where('cl_lecture_type!=', '7');
        return $this->db->get('course_lectures')->result_array();
    }

    public function course_quizes($param = array())
    {
        $course_id = isset($param['course_id'])?$param['course_id']:0;
        $this->db->where('a_course_id', $course_id);
        return $this->db->get('assessments')->result_array();
    }
    
    public function course_quiz_questions($param = array())
    {
        $quiz_ids = isset($param['quiz_ids'])?$param['quiz_ids']:array();
        $this->db->where_in('aq_assesment_id', $quiz_ids);
        return $this->db->get('assessment_questions')->result_array();
    }
    
    public function course_surveys($param = array())
    {
        $course_id = isset($param['course_id'])?$param['course_id']:0;
        $this->db->where('s_course_id', $course_id);
        return $this->db->get('survey')->result_array();
    }
    
    public function course_survey_questions($param = array())
    {
        $survey_ids = isset($param['survey_ids'])?$param['survey_ids']:array();
        $this->db->where_in('sq_survey_id', $survey_ids);
        return $this->db->get('survey_questions')->result_array();
    }
    
    public function course_announcement($param = array())
    {
        $course_id = isset($param['course_id'])?$param['course_id']:0;
        $this->db->where('an_course_id', $course_id);
        return $this->db->get('announcement')->result_array();
    }
    
    public function backup($backup)
    {
        $backup['cbk_backup_date'] = date('Y-m-d G:i:s');
        $backup['cbk_account_id']  = config_item('id');
        if(isset($backup['id']) && $backup['id'])
        {
            $this->db->where('id', $backup['id']);
            $this->db->update('course_backups', $backup);
            $this->db->where('cbk_account_id', config_item('id'));
            return $backup['id'];
        }
        else
        {
            $this->db->insert('course_backups', $backup);
            return $this->db->insert_id();    
        }
    }

    public function backup_section($backup)
    {
        $this->db->insert('course_backups', $backup);
        return $this->db->insert_id();   
    }
    
    public function backups($param = array())
    {
        $course_id              = isset($param['course_id'])?$param['course_id']:0;
        $select                 = isset($param['select'])?$param['select']:'*';
        $excluded_course_id     = isset($param['excluded_course_id'])?$param['excluded_course_id']:0;
        $order_by               = isset($param['order_by'])?$param['order_by']:false;
        $account_id             = isset($param['account_id'])?$param['account_id']:'0';
        $this->db->select($select);
        if($course_id)
        {
            $this->db->where('cbk_course_id', $course_id);
        }
        if($account_id)
        {
            $this->db->where("(cbk_account_id = '0' OR cbk_account_id='".$account_id."')");
        }

        if($excluded_course_id)
        {
            $this->db->where('cbk_course_id!=', $excluded_course_id);
        }
        if($order_by)
        {
            $this->db->order_by("id", $order_by);
        }
        $this->db->where('cbk_account_id', config_item('id'));
        
        return $this->db->get('course_backups')->result_array();
    }
    
    public function backup_details($param = array())
    {
        $backup_id = isset($param['backup_id'])?$param['backup_id']:0;
        $this->db->where('id', $backup_id);
        $this->db->where('cbk_account_id', config_item('id'));
        return $this->db->get('course_backups')->row_array();
    }
    
    public function remove_course_assets($param = array())
    {
        $course_id  = isset($param['course_id'])?$param['course_id']:0;
        $account_id = config_item('id');
        $this->db->trans_start();
        $this->db->query('DELETE FROM announcement WHERE an_course_id = '.$course_id.';');
        $this->db->query('DELETE FROM course_lectures WHERE cl_course_id = '.$course_id.';');
        $this->db->query('DELETE FROM course_subscription WHERE cs_course_id = '.$course_id.';');
        $this->db->query('DELETE FROM course_perfomance WHERE cp_course_id = '.$course_id.';');
        $this->db->query('DELETE FROM course_reviews WHERE rv_course_id = '.$course_id.';');
        $this->db->query('DELETE FROM course_tutors WHERE ct_course_id = '.$course_id.';');
        $this->db->query('DELETE FROM section WHERE s_course_id = '.$course_id.';');
        //discussions need to be removed
        $this->db->trans_complete(); 
        return true;
    }
    
    public function course_sections($param = array())
    {
        $course_id = isset($param['course_id'])?$param['course_id']:0;
        $this->db->where('s_course_id', $course_id);
        return $this->db->get('section')->result_array();
    }

    public function getLastCourseBackupId()
    {
        return $this->db->order_by('id','DESC')->limit('0','1')->get('course_backups')->row();
    }
    
    public function create_sections($sections)
    {
        $result = array();
        $this->db->trans_start();
        foreach($sections as $section)
        {
            $section['s_account_id'] = config_item('id');
            $old_id             = $section['id'];
            $section['id']      = false;
            $this->db->insert('section', $section);
            $section['id']      = $this->db->insert_id();
            $result[$old_id]    = $section;
        }
        $this->db->trans_complete(); 
        return $result;
    }
    
    public function save_lectures($lectures)
    {
        if(!empty($lectures))
        {
            for($i = 0; $i < count($lectures); $i++)
            {
                $lectures[$i]['cl_account_id'] = config_item('id');
            }
        $this->db->insert_batch('course_lectures', $lectures); 
        }
    }
    
    public function save_lecture_queues($lecture_queues)
    {
        $result = array();
        $this->db->trans_start();
        foreach($lecture_queues as $lecture)
        {
            $old_id                 = $lecture['id'];
            $lecture['id']          = false;
            $lecture['cl_account_id'] = config_item('id');
            $this->db->insert('course_lectures', $lecture);
            $lecture['id']          = $this->db->insert_id();
            $result[$old_id]        = $lecture;
        }
        $this->db->trans_complete(); 
        return $result;
    }    
    
    public function save_quizes($quizes)
    {
        $result = array();
        $this->db->trans_start();
        foreach($quizes as $quiz)
        {
            $old_id             = $quiz['id'];
            $quiz['id']         = false;
            $this->db->insert('assessments', $quiz);
            $quiz['id']         = $this->db->insert_id();
            $result[$old_id]    = $quiz;
        }
        $this->db->trans_complete(); 
        return $result;
    }    
    
    public function save_surveys($surveys)
    {
        $result = array();
        $this->db->trans_start();
        foreach($surveys as $survey)
        {
            $old_id             = $survey['id'];
            $survey['id']       = false;
            $survey['s_account_id']= config_item('id');
            $this->db->insert('survey', $survey);
            $survey['id']       = $this->db->insert_id();
            $result[$old_id]    = $survey;
        }
        $this->db->trans_complete(); 
        return $result;
    }    
    
    public function save_quiz_questions($quiz_questions)
    {
        $this->db->insert_batch('assessment_questions', $quiz_questions);         
    }
    
    public function save_survey_questions($survey_questions)
    {
        $this->db->insert_batch('survey_questions', $survey_questions);         
    }
    
    public function save_lecture($data)
    {
        course_lecture_activity_save($data);
        $data['cl_account_id'] = config_item('id');
        if ($data['id']) 
        {
            $this->db->where('id', $data['id']);
            $this->db->where('cl_account_id', config_item('id'));
            $this->db->update('course_lectures', $data);
            return $data['id'];
        } 
        else 
        {
            $this->db->insert('course_lectures', $data);
            return $this->db->insert_id();
        }
    }

    public function updateLectureCopyStatus($data)
    {
        course_lecture_activity_save($data);
        if(isset($data['lecture_ids']) && !empty($data['lecture_ids'])) 
        {
            $this->db->where_in('id', $data['lecture_ids']);
            $this->db->where('cl_account_id', config_item('id'));
            $lecture_ids                    = $data['lecture_ids'];
            unset($data['lecture_ids']);
            $this->db->update('course_lectures', $data);
            return $lecture_ids;
        }
    }

    public function delete($param = array())
    {
        $backup_id = isset($param['backup_id'])?$param['backup_id']:0;
        $this->db->where('id', $backup_id);
        $this->db->where('cbk_account_id', config_item('id'));
        $this->db->delete('course_backups');
    }

    public function save_file_copy_queue($save)
    {
        if(isset($save['id']) && $save['id'] != '')
        {
            $this->db->where('id',$save['id']);
            $this->db->update('file_copy_queue',$save);
            $result = $save['id'];
        }
        else
        {
            $this->db->insert('file_copy_queue', $save);
        }
        //echo $this->db->last_query();
    }

    public function get_copy_queue($param = array())
    {
        $status                 = isset($param['status']) ? $param['status'] : '0';
        $cq_backup_id           = isset($param['cq_backup_id']) ? $param['cq_backup_id'] : false;
        $course_id              = isset($param['course_id']) ? $param['course_id'] : false;
        $id                     = isset($param['id']) ? $param['id'] : false;

        if($id)
        {
            $this->db->where('id', $id);
        }

        if($cq_backup_id)
        {
            $this->db->where('cq_backup_id', $cq_backup_id);
        }

        if($course_id)
        {
            $this->db->where('cq_source_course', $course_id);
            $this->db->where('cq_destination_course', $course_id);
        }

        $this->db->where('cq_status', $status);
        return $this->db->order_by('id','ASC')->get('file_copy_queue')->row_array();
    }

    
}
?>
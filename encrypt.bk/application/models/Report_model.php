<?php
class Report_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }
    public function report_headers($param = array())
    {
        /*$query = "SELECT DISTINCT(upf_field_value) as upf_field_value_group  , upf_field_id
        FROM profile_field_values
        WHERE upf_field_id IN (SELECT id FROM profile_fields WHERE pf_mandatory = '1'  )
        ORDER BY profile_field_values.upf_field_id ASC";*/
        $query = " SELECT us_profile_fields
                             FROM users
                             WHERE us_profile_fields IS NOT NULL AND users.us_role_id = 2 AND users.us_account_id=" . config_item('id');
        $profile_values = $this->db->query($query)->result_array();
        $return = array();
        if (!empty($profile_values)) {
            foreach ($profile_values as $profile_value) {
                $profile_value = $profile_value['us_profile_fields'];
                $profile_value = explode('{#}', $profile_value);
                if (!empty($profile_value)) {
                    foreach ($profile_value as $profile) {
                        $profile = substr($profile, 2);
                        $profile = substr($profile, 0, -2);
                        $profile = explode('{=>}', $profile);
                        if (!isset($return[$profile[0]])) {
                            $return[$profile[0]] = array();
                        }
                        if (!in_array($profile[1], $return[$profile[0]])) {
                            $return[$profile[0]][] = $profile[1];
                        }
                    }
                }
            }
        }
        //echo '<pre>'; print_r($return);die;
        return $return;
    }
    public function report_header_labels()
    {
        $query = "SELECT id, pf_label FROM profile_fields WHERE pf_mandatory = '1' AND pf_account_id='" . config_item('id') . "'";
        return $this->db->query($query)->result_array();
    }
    public function users($param = array())
    {
        $limit = isset($param['limit']) ? $param['limit'] : 0;
        $offset = isset($param['offset']) ? $param['offset'] : 0;
        $order_by = isset($param['order_by']) ? $param['order_by'] : 'id';
        $direction = isset($param['direction']) ? $param['direction'] : 'DESC';
        $status = isset($param['status']) ? $param['status'] : '';
        $role_id = isset($param['role_id']) ? $param['role_id'] : false;
        $count = isset($param['count']) ? $param['count'] : false;
        $keyword = isset($param['keyword']) ? $param['keyword'] : false;
        $locations = isset($param['locations']) ? $param['locations'] : array();
        $header_filters = isset($param['header_filters']) ? $param['header_filters'] : array();
        $course_ids = isset($param['course_ids']) ? $param['course_ids'] : array();
        //generting query from applied header filters
        $header_query = '';
        //echo '<pre>'; print_r($header_filters);die;
        if (!empty($header_filters)) {
            $header_query .= '(';
            $header_where_objects = array();
            foreach ($header_filters as $field_id => $field_values) {
                $where_objects = '';
                $where_objects .= ' ( ';
                if (!empty($field_values)) {
                    $field_filter = array();
                    foreach ($field_values as $field_value) {
                        $field_filter[] = ' us_profile_fields LIKE "%{{' . $field_id . '{=>}' . $field_value . '}}%"';
                    }
                    $where_objects .= implode(' OR ', $field_filter);
                }
                $where_objects .= ') ';
                $header_where_objects[] = $where_objects;
            }
            $header_query .= implode(' AND ', $header_where_objects);
            $header_query .= ')';
        }
        //End
        //Main query started
        if ($count) {
            $global_select = 'SELECT COUNT(*) as total_reports';
        } else {
            $global_select = 'SELECT users.id, users.us_profile_fields, users.us_name, users.us_email, users.us_image, users.us_phone, users.us_native ' . ((!empty($course_ids) || 1 == 1) ? ', course_subscription_cp.course_ids, course_subscription_cp.course_percentages ' : ' ');
        }
        $profile_field_select = 'SELECT users.id';
        $query = ' FROM users';
        if (!empty($course_ids) || 1 == 1) {
            $query .= ' LEFT JOIN (SELECT cs_user_id, GROUP_CONCAT(cs_course_id) as course_ids, GROUP_CONCAT(CONCAT(cs_course_id, "#", cs_percentage)) as course_percentages FROM course_subscription course_subscription_cp GROUP BY cs_user_id ) course_subscription_cp ON users.id = course_subscription_cp.cs_user_id';
        }
        //processing where starts
        $query .= ' WHERE 1';
        $query .= ' AND users.us_role_id = 2 AND users.us_account_id=' . config_item('id');
        if ($header_query) {
            $query .= ' AND (' . $header_query . ')';
        }
        if ($keyword) {
            $query .= ' AND users.us_name LIKE "%' . $keyword . '%"';
        }
        if (!empty($locations)) {
            $location_query = array();
            foreach ($locations as $location) {
                $location_query[] = ' users.us_native = "' . $location . '" ';
            }
            $query .= ' AND (' . (implode(' OR ', $location_query)) . ')';
        }
        //generating where in case of course id
        if (!empty($course_ids)) {
            $course_query = array();
            foreach ($course_ids as $course_id) {
                $course_query[] = ' CONCAT(",", course_subscription_cp.course_ids, ",") LIKE CONCAT("%,", ' . $course_id . ', ",%") ';
            }
            $query .= ' AND (' . implode(' OR ', $course_query) . ')';
            /*SELECT cs_user_id, GROUP_CONCAT(cs_course_id) as course_ids
        FROM course_subscription
        GROUP BY cs_user_id*/
        }
        //End
        //processing where ends
        $query .= ' ORDER BY ' . $order_by . ' ' . $direction;
        if ($limit > 0) {
            $query .= ' LIMIT ' . $offset . ', ' . $limit;
        }
        //Main query ended
        //echo $global_select.$query;die;
        if ($count) {
            $result = $this->db->query($global_select . $query)->row_array();
            $result = $result['total_reports'];
        } else {
            $result = $this->db->query($global_select . $query)->result_array();
        }
        //echo '<pre>'; print_r($result);die;
        return $result;
    }
    public function subscribed_courses()
    {
        $query = 'SELECT course_basics.id, course_basics.cb_title
                    FROM (SELECT cs_course_id FROM course_subscription course_subscription_cp GROUP BY cs_course_id) course_subscription_cp
                    LEFT JOIN course_basics ON course_subscription_cp.cs_course_id = course_basics.id';
        return $this->db->query($query)->result_array();
    }
    public function attempts($param = array())
    {
        $return = array();
        if (isset($param['assessment_id']) && $param['assessment_id'] > 0) {
            $limit = isset($param['limit']) ? $param['limit'] : 0;
            $offset = isset($param['offset']) ? $param['offset'] : 0;
            $limit_query = '';
            if ($limit) {
                $limit_query = 'LIMIT ' . $offset . ', ' . $limit;
            }
            $query = 'SELECT assessment_attempts_cp.*, users.us_name, users.us_image, assessment_report_cp.total_mark
                        FROM (SELECT assessment_attempts.*
                                FROM assessment_attempts
                                JOIN ( SELECT aa_user_id, MAX(aa_attempted_date) as aa_attempted_date
                                        FROM assessment_attempts  WHERE aa_assessment_id = ' . $param['assessment_id'] . '
                                         GROUP BY aa_user_id
                                    ) assessment_attempts_cp
                                ON assessment_attempts.aa_user_id = assessment_attempts_cp.aa_user_id AND assessment_attempts.aa_attempted_date = assessment_attempts_cp.aa_attempted_date) assessment_attempts_cp
                        LEFT JOIN users ON assessment_attempts_cp.aa_user_id = users.id
                        LEFT JOIN (SELECT ar_attempt_id, SUM(ar_mark) as total_mark FROM assessment_report assessment_report_cp  WHERE ar_attempt_id IN (SELECT MAX(assessment_attempts.id) FROM assessment_attempts WHERE aa_assessment_id = ' . $param['assessment_id'] . ' GROUP BY aa_user_id) GROUP BY ar_attempt_id) assessment_report_cp ON assessment_attempts_cp.id = assessment_report_cp.ar_attempt_id
                        WHERE assessment_attempts_cp.aa_assessment_id = ' . $param['assessment_id'] . ' GROUP BY assessment_attempts_cp.aa_user_id ORDER BY assessment_report_cp.total_mark DESC, assessment_attempts_cp.aa_duration ASC ' . $limit_query;
            //echo $query;die;
            $return = $this->db->query($query)->result_array();
        }
        return $return;
    }
    public function attempt_marks($param = array())
    {
        $return = array();
        if (isset($param['attempt_ids']) && $param['attempt_ids'] > 0) {
            $query = 'SELECT ar_attempt_id, SUM(ar_mark) as total_mark '
            . 'FROM assessment_report '
            //. 'WHERE ar_attempt_id IN (SELECT MAX(assessment_attempts.id) as id FROM assessment_attempts WHERE aa_assessment_id = '.$param['assessment_id'].' GROUP BY aa_user_id ) '
             . 'WHERE ar_attempt_id IN (' . implode(',', $param['attempt_ids']) . ') '
                . 'GROUP BY ar_attempt_id';
            $return = $this->db->query($query)->result_array();
        }
        return $return;
    }
    public function assesment($param = array())
    {
        $lecture_id = isset($param['lecture_id']) ? $param['lecture_id'] : 0;
        $course_id = isset($param['course_id']) ? $param['course_id'] : 0;
        $assessment_id = isset($param['assessment_id']) ? $param['assessment_id'] : false;
        $this->db->select('id as assesment_id, a_course_id, a_lecture_id, a_duration');
        if ($course_id) {
            $this->db->where('a_course_id', $course_id);
        }
        if ($lecture_id) {
            $this->db->where('a_lecture_id', $lecture_id);
        }
        if ($assessment_id) {
            $this->db->where('id', $assessment_id);
        }
        return $this->db->get('assessments')->row_array();
    }
    public function attempt($param = array())
    {
        $attempt_id = isset($param['attempt_id']) ? $param['attempt_id'] : 0;
        $return = array();
        if ($attempt_id) {
            $return = $this->db->get_where('assessment_attempts', array('id' => $attempt_id))->row_array();
        }
        return $return;
    }
    public function categories($param = array())
    {
        $return = array();
        $assessment_id = isset($param['assessment_id']) ? $param['assessment_id'] : 0;
        if ($assessment_id) {
            $query = 'SELECT questions_cp.*, questions_category.qc_category_name
                        FROM (SELECT q_category FROM questions questions_cp WHERE id IN (SELECT aq_question_id FROM assessment_questions WHERE aq_assesment_id = ' . $assessment_id . ' ) GROUP BY q_category)
                        questions_cp
                        LEFT JOIN questions_category ON questions_cp.q_category = questions_category.id';
            $return = $this->db->query($query)->result_array();
        }
        return $return;
    }
    public function challenge_zone_categories($param = array())
    {
        $return = array();
        $challenge_zone_id = isset($param['challenge_zone_id']) ? $param['challenge_zone_id'] : 0;
        if ($challenge_zone_id) {
            $query = 'SELECT questions_cp.*, questions_category.qc_category_name
                        FROM (SELECT q_category FROM questions questions_cp WHERE id IN (SELECT czq_question_id FROM challenge_zone_questions WHERE czq_challenge_zone_id = ' . $challenge_zone_id . ' ) GROUP BY q_category)
                        questions_cp
                        LEFT JOIN questions_category ON questions_cp.q_category = questions_category.id';
            $return = $this->db->query($query)->result_array();
        }
        return $return;
    }
    public function category_marks($param = array())
    {
        $return = array();
        $assessment_id = isset($param['assessment_id']) ? $param['assessment_id'] : 0;
        $category_ids = isset($param['category_ids']) ? $param['category_ids'] : 0;
        if ($assessment_id && $category_ids) {
            $query = 'SELECT q_category, SUM(q_positive_mark) as total_marks
                        FROM questions
                        WHERE q_category IN(' . implode(',', $category_ids) . ') AND id IN (SELECT aq_question_id FROM assessment_questions WHERE aq_assesment_id = ' . $assessment_id . ')
                        GROUP BY q_category';
            $return = $this->db->query($query)->result_array();
        }
        return $return;
    }
    public function challenge_zone_category_marks($param = array())
    {
        $return = array();
        $challenge_zone_id = isset($param['challenge_zone_id']) ? $param['challenge_zone_id'] : 0;
        $category_ids = isset($param['category_ids']) ? $param['category_ids'] : 0;
        if ($challenge_zone_id && $category_ids) {
            $query = 'SELECT q_category, SUM(q_positive_mark) as total_marks
                        FROM questions
                        WHERE q_category IN(' . implode(',', $category_ids) . ') AND id IN (SELECT czq_question_id FROM challenge_zone_questions WHERE czq_challenge_zone_id = ' . $challenge_zone_id . ')
                        GROUP BY q_category';
            $return = $this->db->query($query)->result_array();
        }
        return $return;
    }
    public function user_category_marks($param = array())
    {
        $return = array();
        $assessment_id = isset($param['assessment_id']) ? $param['assessment_id'] : 0;
        $attempt_ids = isset($param['attempt_ids']) ? $param['attempt_ids'] : 0;
        $category_id = isset($param['category_id']) ? $param['category_id'] : 0;
        if ($assessment_id && $attempt_ids && $category_id) {
            $query = 'SELECT ar_attempt_id, SUM(ar_mark) as scored_marks
                        FROM assessment_report
                        WHERE ar_attempt_id IN(' . implode(',', $attempt_ids) . ') AND ar_question_id IN (SELECT aq_question_id FROM assessment_questions LEFT JOIN questions ON assessment_questions.aq_question_id  = questions.id WHERE aq_assesment_id = ' . $assessment_id . ' AND questions.q_category = ' . $category_id . ')
                        GROUP BY ar_attempt_id';
            $return = $this->db->query($query)->result_array();
        }
        return $return;
    }
    public function challenge_zone_user_category_marks($param = array())
    {
        $return = array();
        $challenge_zone_id = isset($param['challenge_zone_id']) ? $param['challenge_zone_id'] : 0;
        $attempt_ids = isset($param['attempt_ids']) ? $param['attempt_ids'] : 0;
        $category_id = isset($param['category_id']) ? $param['category_id'] : 0;
        if ($challenge_zone_id && $attempt_ids && $category_id) {
            $query = 'SELECT czr_attempt_id, SUM(czr_mark) as scored_marks
                        FROM challenge_zone_report
                        WHERE czr_attempt_id IN(' . implode(',', $attempt_ids) . ') AND czr_question_id IN (SELECT czq_question_id FROM challenge_zone_questions LEFT JOIN questions ON challenge_zone_questions.czq_question_id  = questions.id WHERE czq_challenge_zone_id = ' . $challenge_zone_id . ' AND questions.q_category = ' . $category_id . ')
                        GROUP BY czr_attempt_id';
            $return = $this->db->query($query)->result_array();
        }
        return $return;
    }
    //for chalenge zone
    public function challenge_attempts($param = array())
    {
        $return = array();
        if (isset($param['challenge_zone_id']) && $param['challenge_zone_id'] > 0) {
            $limit = isset($param['limit']) ? $param['limit'] : 0;
            $offset = isset($param['offset']) ? $param['offset'] : 0;
            $limit_query = '';
            if ($limit) {
                $limit_query = 'LIMIT ' . $offset . ', ' . $limit;
            }
            $query = 'SELECT challenge_zone_attempts_cp.*, users.us_name, users.us_image, challenge_zone_report_cp.total_mark
                            FROM (
                                SELECT challenge_zone_attempts.*
                                FROM challenge_zone_attempts
                                JOIN ( SELECT cza_user_id, MAX(cza_attempted_date) as cza_attempted_date
                                        FROM challenge_zone_attempts  WHERE cza_challenge_zone_id = ' . $param['challenge_zone_id'] . '
                                         GROUP BY cza_user_id
                                    ) challenge_zone_attempts_cp
                                ON challenge_zone_attempts.cza_user_id = challenge_zone_attempts_cp.cza_user_id AND challenge_zone_attempts.cza_attempted_date = challenge_zone_attempts_cp.cza_attempted_date
                                 ) challenge_zone_attempts_cp
                        LEFT JOIN users ON challenge_zone_attempts_cp.cza_user_id = users.id
                        LEFT JOIN (SELECT czr_attempt_id, SUM(czr_mark) as total_mark FROM challenge_zone_report challenge_zone_report_cp  WHERE czr_attempt_id IN (SELECT MAX(challenge_zone_attempts.id) FROM challenge_zone_attempts WHERE cza_challenge_zone_id = ' . $param['challenge_zone_id'] . ' GROUP BY cza_user_id) GROUP BY czr_attempt_id) challenge_zone_report_cp ON challenge_zone_attempts_cp.id = challenge_zone_report_cp.czr_attempt_id
                        WHERE challenge_zone_attempts_cp.cza_challenge_zone_id = ' . $param['challenge_zone_id'] . ' GROUP BY challenge_zone_attempts_cp.cza_user_id ORDER BY challenge_zone_report_cp.total_mark DESC, challenge_zone_attempts_cp.cza_duration ASC ' . $limit_query;
            //echo $query;die;
            $return = $this->db->query($query)->result_array();
        }
        return $return;
    }
    public function challenge_attempt_marks($param = array())
    {
        $return = array();
        if (isset($param['attempt_ids']) && $param['attempt_ids'] > 0) {
            $query = 'SELECT czr_attempt_id, SUM(czr_mark) as total_mark '
            . 'FROM challenge_zone_report '
            . 'WHERE czr_attempt_id IN (' . implode(',', $param['attempt_ids']) . ') '
                . 'GROUP BY czr_attempt_id';
            $return = $this->db->query($query)->result_array();
        }
        return $return;
    }
    public function challenge_zone($param = array())
    {
        $return = array();
        $challenge_zone_id = isset($param['challenge_zone_id']) ? $param['challenge_zone_id'] : false;
        if ($challenge_zone_id) {
            $this->db->select('id as challenge_zone_id, cz_title, cz_duration');
            $this->db->where('id', $challenge_zone_id);
            $return = $this->db->get('challenge_zone')->row_array();
        }
        return $return;
    }
    public function challenge_attempt($param = array())
    {
        $attempt_id = isset($param['attempt_id']) ? $param['attempt_id'] : 0;
        $return = array();
        if ($attempt_id) {
            $return = $this->db->get_where('challenge_zone_attempts', array('id' => $attempt_id))->row_array();
        }
        return $return;
    }
    //End
    /*
     * created by thanveeer
     * purpose : fetch the list of user and their report with assignmnt
     */
    public function assignment_attendees($param = array())
    {
        $lecture_id = isset($param['lecture_id']) ? $param['lecture_id'] : false;
        $course_id = isset($param['course_id']) ? $param['course_id'] : false;
        $filter = isset($param['filter']) ? $param['filter'] : false;
        $keyword = isset($param['keyword']) ? $param['keyword'] : false;
        $sort_by = (isset($param['sort_by']) && ($param['sort_by'] != 'all') && ($param['sort_by'] != '')) ? $param['sort_by'] : 'id_desc';
        $tutor = (isset($param['tutor']) && ($param['tutor'] != 'all')) ? $param['tutor'] : false;
        $institute_id = isset($param['institute_id']) ? $param['institute_id'] : false;
        if ($institute_id) {
            $institute_id = ($institute_id != 'all') ? $institute_id : false;
        }
        $batch = isset($param['batch_id']) ? $param['batch_id'] : false;
        if ($batch) {
            $batch = ($batch != 'all') ? $batch : false;
        }
        $limit = isset($param['limit']) ? $param['limit'] : 0;
        $offset = isset($param['offset']) ? $param['offset'] : 0;
        $count = isset($param['count']) ? $param['count'] : false;
        $user_institute = isset($param['user_institute']) ? $param['user_institute'] : '0';
        $where = '';
        $return = array();

        if ($filter) {
            switch ($filter) {
                case "all":
                    $where = ' AND institutes.ib_deleted = "0"';
                    break;
                case "submitted":
                    $where = ' AND desc_test_user_report.dtua_user_id IS NOT NULL ';
                    break;
                case "not_submitted":
                    $where = ' AND desc_test_user_report.dtua_user_id IS NULL ';
                    break;
                case "late_submit":
                    $where = ' AND descrptive_tests.dt_last_date < desc_test_user_report.created_date AND desc_test_user_report.dtua_user_id IS NOT NULL';
                    break;
                case "to_evaluate":
                    $where = ' AND desc_test_user_report.dtua_evaluated = "0"';
                    break;
                case "A+":
                case "A":
                case "B+":
                case "B":
                case "C+":
                case "C":
                case "D+":
                case "D":
                case "E":
                    $where = ' AND desc_test_user_report.dtua_grade = "' . $filter . '"';
                    break;
            }
        }
        if ($keyword) {
            $where .= ' AND users.us_name LIKE "%' . $keyword . '%" ';
        }
        if ($institute_id) {
            $where .= ' AND users.us_institute_id = "' . $institute_id . '" ';
        }
        if ($batch) {
            $where .= ' AND FIND_IN_SET("' . $batch . '",users.us_groups)';
        }
        if ($tutor) {
            $where .= ' AND desc_test_user_report.dtua_assigned_to = ' . $tutor;
        }
        $limit_query = '';
        if (!$count) {
            if ($limit > 0) {
                $limit_query = 'LIMIT ' . $offset . ',' . $limit;
            }
        }
        $order_by = '';
        if ($sort_by) {
            switch ($sort_by) {
                case "name_a_z":
                    $order_by = ' ORDER BY users.us_name';
                    break;
                case "marks_high_low":
                    $order_by = ' ORDER BY desc_test_user_report.mark DESC';
                    break;
                case "marks_low_high":
                    $order_by = ' ORDER BY desc_test_user_report.mark ASC';
                    break;
                case "id_desc":
                    $order_by = ' ORDER BY desc_test_user_report.id DESC';
                    break;
                case "all":
                    $order_by = ' ORDER BY desc_test_user_report.id DESC';
                    break;

            }
        }
        
        if ($lecture_id && $course_id) {
            $query = "SELECT desc_test_user_report.dtua_course_id as cs_course_id, users.id as cs_user_id, users.us_name, users.us_image,faculty.us_name as faculty_name,faculty.us_image as faculty_image,users.us_phone,users.us_groups,users.us_institute_id, descrptive_tests.id as assignment_id, descrptive_tests.dt_last_date, desc_test_user_report.dtua_user_id, desc_test_user_report.mark,DATE_FORMAT(desc_test_user_report.created_date,'%d-%m-%Y') as created_date, desc_test_user_report.dtua_lecture_id, desc_test_user_report.dtua_evaluated, desc_test_user_report.id as attempt_id,desc_test_user_report.dtua_assigned_to,desc_test_user_report.dtua_grade
                        FROM descrptive_test_user_answered desc_test_user_report
                        LEFT JOIN descrptive_tests ON desc_test_user_report.dtua_lecture_id = descrptive_tests.dt_lecture_id
                        LEFT JOIN users ON desc_test_user_report.dtua_user_id = users.id
                        LEFT JOIN users faculty ON desc_test_user_report.dtua_assigned_to = faculty.id";
            if ($filter == "all") {
                $query .= " LEFT JOIN institute_basics institutes ON users.us_institute_id = institutes.id";
            }
            $query .= " WHERE desc_test_user_report.dtua_lecture_id = " . $lecture_id . " " . $where . " " . $order_by . " " . $limit_query;
            if ($count) {
                $return = $this->db->query($query)->num_rows();
            } else {
                $return = $this->db->query($query)->result_array();
            }
            //echo $this->db->last_query();die;
        }
        return $return;
    }
    public function lecture_completed_status($param = array())
    {
        $user_id = isset($param['user_id']) ? $param['user_id'] : false;
        $course_id = isset($param['course_id']) ? $param['course_id'] : false;
        $return = array();
        if ($user_id && $course_id) {
            $query = 'SELECT course_lectures.id, course_lectures.cl_lecture_name, lecture_log_cp.*
                        FROM  course_lectures
                        LEFT JOIN (SELECT ll_user_id, ll_lecture_id, ll_attempt,
                                            (CASE
                                                WHEN ll_attempt > 1 THEN 100
                                                ELSE ll_percentage
                                            END ) AS ll_percentage_new
                                    FROM lecture_log lecture_log_cp
                                    WHERE ll_user_id = ' . $user_id . ' AND ll_lecture_id IN (SELECT id FROM course_lectures WHERE cl_course_id = ' . $course_id . ' AND cl_deleted = "0" AND cl_status = "1")
                                    ORDER BY ll_user_id ASC
                                ) lecture_log_cp ON course_lectures.id = lecture_log_cp.ll_lecture_id WHERE course_lectures.cl_course_id = ' . $course_id . ' AND course_lectures.cl_deleted = "0" AND course_lectures.cl_status = "1" ORDER BY course_lectures.id ASC';
            $return = $this->db->query($query)->result_array();
        }
        return $return;
    }
    public function user_course_mark($param = array())
    {
        $course_id = isset($param['course_id']) ? $param['course_id'] : false;
        $this->db->where(array('cl_course_id' => $course_id, 'cl_deleted' => '0'));
        $total_lectures = $this->db->count_all_results('course_lectures');
        $return = array();
        if ($course_id) {
            $query = 'SELECT ll_user_id, ROUND((SUM(ll_marks)/' . $total_lectures . ')) as marks_percentage
                        FROM lecture_log
                        WHERE lecture_log.ll_lecture_id IN (SELECT id FROM course_lectures WHERE cl_course_id = ' . $course_id . ' AND cl_deleted = "0")
                        GROUP BY ll_user_id
                        ORDER BY lecture_log.ll_user_id ASC ';
            $return = $this->db->query($query)->result_array();
        }
        return $return;
    }
    public function enrolled_report($param = array())
    {
        $course_id = isset($param['course_id']) ? $param['course_id'] : false;
        $count = isset($param['count']) ? $param['count'] : false;
        $limit = isset($param['limit']) ? $param['limit'] : 0;
        $offset = isset($param['offset']) ? $param['offset'] : 0;
        $approved = isset($param['approved']) ? $param['approved'] : '';
        $expired = isset($param['expired']) ? $param['expired'] : '';
        $certificate_issued = isset($param['certificate_issued']) ? $param['certificate_issued'] : '';
        $keyword = isset($param['keyword']) ? $param['keyword'] : '';
        $filter = isset($param['filter']) ? $param['filter'] : 0;
        $filters = isset($param['filters']) ? $param['filters'] : array();
        $institute_id = isset($param['institute']) ? $param['institute'] : 0;
        $total_lectures = isset($param['total_lectures']) ? $param['total_lectures'] : 0;
        $this->db->select('course_subscription.id, course_subscription.cs_course_id, course_subscription.cs_user_id, users.us_name, lecture_log_report.*');
        $this->db->join('users', 'course_subscription.cs_user_id = users.id', 'left');
        $this->db->join('(SELECT ll_user_id, ROUND((SUM((CASE
                                                    WHEN ll_attempt > 1 THEN 100
                                                    ELSE ll_percentage
                                                END ))/' . $total_lectures . ')) as completed_percentage, ROUND((SUM(ll_marks)/' . $total_lectures . ')) as marks_percentage
                            FROM lecture_log lecture_log_report
                            WHERE lecture_log_report.ll_lecture_id IN (SELECT id FROM course_lectures WHERE cl_course_id = ' . $course_id . ' AND cl_deleted = "0")
                            GROUP BY ll_user_id
                            ORDER BY lecture_log_report.ll_user_id ASC
                            ) lecture_log_report', 'course_subscription.cs_user_id=lecture_log_report.ll_user_id', 'left');
        if ($course_id) {
            $this->db->where('course_subscription.cs_course_id', $course_id);
        }
        if ($limit) {
            $this->db->limit($limit, $offset);
        }
        if ($approved != '') {
            $this->db->where('course_subscription.cs_approved', $approved);
        }
        if ($institute_id) {
            $this->db->where('us_institute_id', $institute_id);
        }
        if ($expired != '') {
            if ($expired == '1') {
                $this->db->where('course_subscription.cs_end_date <', date('Y-m-d'));
            } else {
                $this->db->where('course_subscription.cs_end_date >=', date('Y-m-d'));
            }
        }
        if ($certificate_issued != '') {
            $this->db->where('course_subscription.cs_certificate_issued', $certificate_issued);
        }
        if ($keyword) {
            $this->db->like('users.us_name', $keyword);
        }
        if ($filter) {
            switch ($filter) {
                case 'active':
                    $this->db->where('course_subscription.cs_approved', '1');
                    $this->db->where('course_subscription.cs_end_date >=', date('Y-m-d'));
                    break;
                case 'suspended':
                    $this->db->where('course_subscription.cs_approved', '0');
                    break;
                case 'completed':
                    $check_completion = true;
                    $method = $filter;
                    break;
                case 'incompleted':
                    $check_completion = true;
                    $method = $filter;
                    break;
                case 'not_started':
                    $check_completion = true;
                    $method = $filter;
                    break;
                default:
                    break;
            }
        }
        //to prevent the deleted resoceds
        $this->db->where("users.id is NOT NULL");
        //end
        //processing where condition for filter
        $filter_queries = array();
        $filter_queries['all'] = ' 1 ';
        $filter_queries['completed'] = ' (completed_percentage > 96) ';
        $filter_queries['not_started'] = ' (completed_percentage = 0 OR completed_percentage IS NULL) ';
        $filter_queries['A+'] = ' (FLOOR(marks_percentage/10) = 10) OR (FLOOR(marks_percentage/10) = 9 ) ';
        $filter_queries['A'] = ' (FLOOR(marks_percentage/10) = 8) ';
        $filter_queries['B+'] = ' (FLOOR(marks_percentage/10) = 7) ';
        $filter_queries['B'] = ' (FLOOR(marks_percentage/10) = 6) ';
        $filter_queries['C+'] = ' (FLOOR(marks_percentage/10) = 5) ';
        $filter_queries['C'] = ' (FLOOR(marks_percentage/10) = 4) ';
        $filter_queries['D+'] = ' (FLOOR(marks_percentage/10) = 3) ';
        $filter_queries['D'] = ' (FLOOR(marks_percentage/10) = 2) ';
        $filter_queries['E'] = ' (FLOOR(marks_percentage/10) = 1) OR (FLOOR(marks_percentage/10) = 0 ) ';
        $where = array();
        if (!empty($filters)) {
            foreach ($filters as $filter_key) {
                $where[] = $filter_queries[$filter_key];
            }
            if (!empty($where)) {
                $this->db->where('(' . implode(' OR ', $where) . ')');
            }
        }
        //End
        $result = $this->db->get('course_subscription')->result_array();
        //echo '<pre>'; print_r($result);die;
        if ($count) {
            return sizeof($result);
        } else {
            return $result;
        }
    }
    public function enrolled_course($param = array())
    {
       
        $user_id        = isset($param['user_id']) ? $param['user_id'] : false;
        $courses_only   = isset($param['courses_only']) ? $param['courses_only'] : false; 
        $order_by       = isset($param['order_by']) ? $param['order_by'] : false;
        $order_by_date  = isset($param['order_by_date']) ? $param['order_by_date'] : false;
        $this->db->select('course_basics.id as course_id, course_basics.cb_title,course_basics.cb_slug,course_subscription.cs_course_id,course_subscription.cs_approved,course_subscription.cs_course_validity_status,course_subscription.cs_end_date,course_basics.cb_category,course_basics.cb_image,course_lectures.cl_lecture_name,course_subscription.cs_percentage,course_subscription.cs_auto_grade,course_subscription.cs_manual_grade, course_subscription.cs_bundle_id, course_subscription.cs_last_played_lecture, "course" as item_type');
        if ($user_id) {
            $this->db->where('cs_user_id', $user_id);
        }

        if ($courses_only) {
            $this->db->where('cs_bundle_id', '0');
        }
        $this->db->where('cs_account_id', config_item('id'));
        $this->db->where('course_subscription.cs_end_date >=', date('Y-m-d'));
        
        $this->db->join('course_basics', 'course_subscription.cs_course_id = course_basics.id', 'inner');
        $this->db->join('course_lectures', 'course_subscription.cs_last_played_lecture = course_lectures.id', 'left');
       
        if ($order_by_date = true)
        {
            $this->db->order_by('course_subscription.updated_date','desc');
        }
        else
        {
            if ($order_by = true)
            {
                $this->db->order_by('course_subscription.id','desc');
            }
        }
       
        $result = $this->db->get('course_subscription')->result_array();
      
        return $result;
    }
    public function my_courses($param = array())
    {
        $user_id        = isset($param['user_id']) ? $param['user_id'] : false;
        $courses_only   = isset($param['courses_only']) ? $param['courses_only'] : false; 
        $this->db->select('course_basics.id as course_id, course_subscription.id as subscribe_id, course_basics.cb_title,course_basics.cb_deleted,course_basics.cb_image,course_subscription.cs_percentage,course_subscription.cs_approved,course_subscription.cs_course_validity_status,course_subscription.created_date,course_subscription.cs_end_date,"course" as item_type');
        if ($user_id) {
            $this->db->where('cs_user_id', $user_id);
        }
        if ($courses_only) {
            $this->db->where('cs_bundle_id', '0');
        }
        $this->db->where('course_subscription.cs_end_date >=', date('Y-m-d'));
        $this->db->join('course_basics', 'course_subscription.cs_course_id = course_basics.id', 'inner');
        $this->db->order_by("course_subscription.id", "desc");
        $result = $this->db->get('course_subscription')->result_array();
        
        return $result;
    }
    public function user_course_assessment_report($param = array())
    {
        $user_id = isset($param['user_id']) ? $param['user_id'] : false;
        $course_id = isset($param['course_id']) ? $param['course_id'] : false;
        $return = array();
        if ($user_id && $course_id) {
            $query = "SELECT assessments.a_pass_percentage, course_lectures.cl_lecture_name,course_lectures.id AS lecture_id,course_lectures.cl_course_id, course_lectures.cl_limited_access, assessment_attempts_cp.*, lecture_log_cp.*
                            FROM assessments
                            LEFT JOIN (
                                        SELECT assessment_attempts.*, assessment_attempts_cp.total_attempts
                                        FROM (SELECT aa_assessment_id, aa_user_id, max(aa_attempted_date) as aa_attempted_date, COUNT(id) as total_attempts
                                                FROM assessment_attempts assessment_attempts_cp
                                                GROUP BY CONCAT(aa_assessment_id, '_', aa_user_id)
                                             ) assessment_attempts_cp
                                        LEFT JOIN assessment_attempts ON assessment_attempts.aa_attempted_date = assessment_attempts_cp.aa_attempted_date AND assessment_attempts.aa_assessment_id = assessment_attempts_cp.aa_assessment_id AND assessment_attempts.aa_user_id = assessment_attempts_cp.aa_user_id
                                        WHERE assessment_attempts.aa_user_id = " . $user_id . "
                                        ORDER BY assessment_attempts.aa_attempted_date ASC
                                    ) assessment_attempts_cp ON assessments.id = assessment_attempts_cp.aa_assessment_id
                            LEFT JOIN course_lectures ON assessments.a_lecture_id = course_lectures.id
                            LEFT JOIN (
                                        SELECT ll_marks, ll_lecture_id
                                        FROM lecture_log lecture_log_cp
                                        WHERE ll_lecture_id IN (SELECT id FROM course_lectures WHERE cl_course_id = " . $course_id . " AND cl_deleted = '0' AND cl_status = '1' AND cl_lecture_type='3'  ) AND ll_user_id='" . $user_id . "'
                                      ) lecture_log_cp ON assessments.a_lecture_id = lecture_log_cp.ll_lecture_id
                            WHERE course_lectures.cl_course_id = " . $course_id;
            $return = $this->db->query($query)->result_array();
        }
        return $return;
    }
    public function course_completion($param = array())
    {
        $user_id = isset($param['user_id']) ? $param['user_id'] : false;
        $course_id = isset($param['course_id']) ? $param['course_id'] : false;
        $limit = isset($param['limit']) ? $param['limit'] : false;
        $offset = isset($param['offset']) ? $param['offset'] : '0';
        $limit_query = $limit ? ' LIMIT ' . $offset . ',' . $limit : '';
        $return = array();
        if ($user_id && $course_id) {
            $query = "SELECT SUM(ll_percentage_new)/COUNT(*) as course_percentage, course_basics.cb_title, course_basics.cb_image , COUNT(*) as total_lectures
                            FROM  course_lectures
                            LEFT JOIN course_basics ON course_lectures.cl_course_id = course_basics.id
                            LEFT JOIN (SELECT ll_user_id, ll_lecture_id, ll_attempt,
                                                (CASE
                                                    WHEN ll_attempt > 1 THEN 100
                                                    ELSE ll_percentage
                                                END ) AS ll_percentage_new
                                        FROM lecture_log lecture_log_cp
                                        WHERE ll_user_id = " . $user_id . " AND ll_lecture_id IN (SELECT id FROM course_lectures WHERE cl_course_id = " . $course_id . " AND cl_deleted = '0' AND cl_status = '1')
                                        ORDER BY ll_user_id ASC
                                    ) lecture_log_cp ON course_lectures.id = lecture_log_cp.ll_lecture_id
                                    WHERE course_lectures.cl_course_id = " . $course_id . " AND cl_deleted = '0' AND cl_status = '1'" . $limit_query;
            $return = $this->db->query($query)->row_array();
            //echo $this->db->last_query();die;
        }
        return $return;
    }
    /* Written By Alex */
    public function course_enrolled_students($param = array())
    {
        $course_id = isset($param['course_id']) ? $param['course_id'] : 0;
        $student_name = isset($param['student_name']) ? $param['student_name'] : '';
        $offset = isset($param['offset']) ? $param['offset'] : 0;
        $limit = isset($param['limit']) ? $param['limit'] : 0;
        $user_id = isset($param['user_id']) ? $param['user_id'] : false;
        $institute_id = (isset($param['role_param']) && $param['role_param']['us_role_id'] == 8) ? $param['role_param']['id'] : false;
        if (isset($param['select'])) {
            $this->db->select($param['select']);
        } else {
            $this->db->select('users.id,users.us_name,users.us_image,course_subscription.cs_course_id,course_subscription.cs_approved');
        }
        if ($limit != 0) {
            $this->db->limit($limit, $offset);
        }
        if ($user_id) {
            $this->db->where('users.id', $user_id);
        }
        if ($student_name != '') {
            $this->db->where("users.us_name LIKE '%" . $student_name . "%'");
        }
        if ($institute_id) {
            $this->db->where('users.us_institute_id', $institute_id);
        }
        $this->db->where('users.us_role_id', '2');
        $this->db->where('users.us_account_id', config_item('id'));
        if ($course_id != '') {
            $this->db->where_in('cs_course_id', $course_id);
        }
        $this->db->group_by('course_subscription.cs_user_id');
        $this->db->join('users', 'course_subscription.cs_user_id = users.id');
        $this->db->order_by('users.us_name', 'ASC');
        if ($user_id) {
            $result = $this->db->get('course_subscription')->row_array();
        } else {
            $result = $this->db->get('course_subscription')->result_array();
        }
        //echo $this->db->last_query();die;
        return $result;
    }
    /* End of Written By Alex */
    public function get_survey_report($params = array())
    {
        $survey_id = isset($params['survey_id']) ? $params['survey_id'] : false;
        $tutor_id = isset($params['tutor_id']) ? $params['tutor_id'] : false;
        if ($survey_id) {
            $select = array(
                'survey.s_name',
                'survey.s_tutor_name',
                'survey_user_response.sur_user_name',
                'survey_user_response.sur_question',
                'survey_user_response.sur_answer',
                'survey_user_response.sur_question_id'
            );
            $this->db->select($select);
            $this->db->join('survey', 'survey.id=survey_user_response.sur_survey_id');
            if ($tutor_id) {
                $this->db->where('survey.s_tutor_id', $tutor_id);
            }
            $this->db->where('survey_user_response.sur_survey_id', $survey_id);
            $return = $this->db->get('survey_user_response')->result_array();
            // echo $this->db->last_query();die;
            return $return;
        }
    }
    public function assign_faculty($param = array())
    {
        $this->db->where('id', $param['id']);
        $result = $this->db->update('descrptive_test_user_answered', array('dtua_assigned_to' => $param['dtua_assigned_to']));
        return $result;
    }
    public function assign_grade($param = array())
    {
        $this->db->where('id', $param['id']);
        $result = $this->db->update('descrptive_test_user_answered', $param);
        return $result;
    }
    public function check_if_exist_institute($param)
    {
        $count = isset($param['count']) ? $param['count'] : false;
        $institute_id = isset($param['institute_id']) ? $param['institute_id'] : false;
        $select = isset($param['select']) ? $param['select'] : '*';
        $course_id = isset($param['course_id']) ? $param['course_id'] : false;
        $this->db->select($select);
        if ($institute_id) {
            $this->db->where('cp_institute_id', $institute_id);
        }
        if ($course_id) {
            $this->db->where('cp_course_id', $course_id);
        }
        $this->db->limit(1);
        $this->db->from('course_perfomance');
        $result = $this->db->get();
        if ($count) {
            return $result->num_rows();
        } else {
            return $result->row_array();
        }
    }
    /* Quiz report generation */
    public function assessment_attendees($param = array())
    {

        $lecture_id = isset($param['lecture_id']) ? $param['lecture_id'] : false;
        $course_id = isset($param['course_id']) ? $param['course_id'] : false;
        $filter = isset($param['filter']) ? $param['filter'] : false;
        $keyword = isset($param['keyword']) ? $param['keyword'] : false;
        $sort_by = (isset($param['sort_by']) && ($param['sort_by'] != 'all')) ? $param['sort_by'] : 'id_desc';
        $tutor = (isset($param['tutor']) && ($param['tutor'] != 'all')) ? $param['tutor'] : false;
        $institute_id = isset($param['institute_id']) ? $param['institute_id'] : false;
        if ($institute_id) {
            $institute_id = ($institute_id != 'all') ? $institute_id : false;
        }
        $batch = isset($param['batch_id']) ? $param['batch_id'] : false;
        if ($batch) {
            $batch = ($batch != 'all') ? $batch : false;
        }
        $limit = isset($param['limit']) ? $param['limit'] : 0;
        $offset = isset($param['offset']) ? $param['offset'] : 0;
        $count = isset($param['count']) ? $param['count'] : false;
        $user_institute = isset($param['user_institute']) ? $param['user_institute'] : '0';
        $where = '';
        $return = array();
        if ($filter) {
            switch ($filter) {
                case "all":
                    $where = ' AND institutes.ib_deleted = "0"';
                    break;
                case "submitted":
                    $where = ' AND desc_test_user_report.aa_user_id IS NOT NULL ';
                    break;
                case "not_submitted":
                    $where = ' AND desc_test_user_report.aa_user_id IS NULL ';
                    break;
                case "late_submit":
                    $where = ' AND desc_test_user_report.aa_to_date < desc_test_user_report.aa_attempted_date AND desc_test_user_report.aa_user_id IS NOT NULL';
                    break;
                case "to_evaluate":
                    $where = ' AND desc_test_user_report.aa_valuated = "0"';
                    break;
                case "A+":
                case "A":
                case "B+":
                case "B":
                case "C+":
                case "C":
                case "D+":
                case "D":
                case "E":
                    $where = ' AND desc_test_user_report.aa_grade = "' . $filter . '"';
                    break;
            }
        }
        if ($keyword) {
            $where .= ' AND users.us_name LIKE "%' . $keyword . '%" ';
        }
        if ($institute_id) {
            $where .= ' AND users.us_institute_id = "' . $institute_id . '" ';
        }
        if ($batch) {
            $where .= ' AND FIND_IN_SET("' . $batch . '",users.us_groups)';
        }
        if ($tutor) {
            $where .= ' AND desc_test_user_report.aa_valuated_by = ' . $tutor;
        }
        $limit_query = '';
        if (!$count) {
            if ($limit > 0) {
                $limit_query = 'LIMIT ' . $offset . ',' . $limit;
            }
        }
        $order_by = '';
        if ($sort_by) {
            switch ($sort_by) {
                case "name_a_z":
                    $order_by = ' ORDER BY users.us_name';
                    break;
                case "marks_high_low":
                    $order_by = ' ORDER BY desc_test_user_report.aa_mark_scored DESC';
                    break;
                case "marks_low_high":
                    $order_by = ' ORDER BY desc_test_user_report.aa_mark_scored ASC';
                    break;
                case "id_desc":
                    $order_by = ' ORDER BY desc_test_user_report.id DESC';
                    break;
            }
        }
        if ($lecture_id && $course_id) {
            $query = 'SELECT assessments.id as assessment_id,users.us_name, users.us_image,faculty.us_name as faculty_name,faculty.us_image as faculty_image,users.us_phone,users.us_groups,users.us_institute_id, desc_test_user_report.aa_to_date, desc_test_user_report.aa_user_id, desc_test_user_report.aa_total_mark,DATE_FORMAT(desc_test_user_report.aa_attempted_date,"%d-%m-%Y") as aa_attempted_date, desc_test_user_report.aa_lecture_id, desc_test_user_report.aa_valuated, desc_test_user_report.id as attempt_id,desc_test_user_report.aa_valuated_by,desc_test_user_report.aa_duration,desc_test_user_report.aa_grade,desc_test_user_report.aa_mark_scored
                                    FROM assessment_attempts desc_test_user_report
                                    LEFT JOIN assessments ON desc_test_user_report.aa_lecture_id = assessments.a_lecture_id
                                    LEFT JOIN users ON desc_test_user_report.aa_user_id = users.id
                                    LEFT JOIN users faculty ON desc_test_user_report.aa_valuated_by = faculty.id';
            if ($filter == "all") {
                $query .= ' LEFT JOIN institute_basics institutes ON users.us_institute_id = institutes.id';
            }
            $query .= ' WHERE desc_test_user_report.aa_latest="1" AND desc_test_user_report.aa_completed="1" AND desc_test_user_report.aa_lecture_id = ' . $lecture_id . '' . $where . ' ' . $order_by . ' ' . $limit_query;

            if ($count) {
                $return = $this->db->query($query)->num_rows();
            } else {
                $return = $this->db->query($query)->result_array();
            }
            //echo $this->db->last_query();die;
        }
        return $return;
    }
    public function assessment_not_submitted_users($param = array())
    {
        $lecture_id = isset($param['lecture_id']) ? $param['lecture_id'] : false;
        $course_id = isset($param['course_id']) ? $param['course_id'] : false;
        if ($lecture_id && $course_id) {
            $query = 'SELECT DISTINCT course_subscription.cs_course_id, course_subscription.cs_user_id, users.us_name, users.us_email, desc_test_user_report.aa_lecture_id,desc_test_user_report.aa_user_id
                        FROM course_subscription
                        LEFT JOIN users ON course_subscription.cs_user_id = users.id
                        LEFT JOIN (
                                    SELECT assessments.id as assessment_id, desc_test_user_report.aa_to_date, desc_test_user_report.aa_user_id,  desc_test_user_report.aa_lecture_id
                                    FROM assessment_attempts desc_test_user_report
                                    LEFT JOIN assessments ON desc_test_user_report.aa_lecture_id = assessments.a_lecture_id
                                    WHERE desc_test_user_report.aa_lecture_id = ' . $lecture_id . '
                                   ) desc_test_user_report ON course_subscription.cs_user_id = desc_test_user_report.aa_user_id
                        WHERE course_subscription.cs_course_id = ' . $course_id . ' AND desc_test_user_report.aa_user_id IS NULL';
            $return = $this->db->query($query)->result_array();
            return $return;
        }
    }
    public function assign_assessment_faculty($param = array())
    {
        $this->db->where('id', $param['id']);
        $result = $this->db->update('assessment_attempts', array('aa_valuated_by' => $param['aa_valuated_by']));
        return $result;
    }
    public function assign_assessment_grade($param = array())
    {
        $this->db->where('id', $param['id']);
        $result = $this->db->update('assessment_attempts', $param);
        return $result;
    }
    public function assessment_attempts($param = array())
    {
        $attempt_id = isset($param['id']) ? $param['id'] : 0;
        $select = isset($param['select']) ? $param['select'] : false;
        if ($select) {
            $this->db->select($select);
        }
        $this->db->where('id', $attempt_id);
        $return = $this->db->get('assessment_attempts')->row_array();
        //echo $this->db->last_query();die;
        return $return;
    }
    public function save_assessment_attempts($data = array())
    {
        $this->db->where('id', $data['id']);
        $this->db->update('assessment_attempts', $data);
        return true;
        //echo $this->db->last_query();die;
    }
    public function save_assessment_valuation($evaluations)
    {
        $evaluation_chunks = array_chunk($evaluations, 50);
        if (!empty($evaluation_chunks)) {
            foreach ($evaluation_chunks as $evaluations) {
                $this->db->trans_start();
                foreach ($evaluations as $evaluation) {
                    $this->db->query("UPDATE assessment_report SET ar_mark = " . $evaluation['ar_mark'] . " WHERE ar_attempt_id = " . $evaluation['ar_attempt_id'] . " AND ar_question_id = " . $evaluation['ar_question_id'] . ";");
                }
                $this->db->trans_complete();
            }
        }
    }

    public function log_activities($param = array())
    {
        $keyword        = isset($param['keyword']) ? $param['keyword'] : false;
        $log_date_start = isset($param['log_date_start']) ? $param['log_date_start'] : false;
        $log_date_end   = isset($param['log_date_end']) ? date('Y-m-d', strtotime($param['log_date_end']."+1 days")) : false;
        $usertype       = isset($param['usertype']) ? $param['usertype'] : false;
        $userid         = isset($param['userid']) ? $param['userid'] : false;
        $limit          = isset($param['limit']) ? $param['limit'] : 0;
        $offset         = isset($param['offset']) ? $param['offset'] : 0;
        $count          = isset($param['count']) ? $param['count'] : false;
        $select         = isset($param['select']) ? $param['select'] : '*';
        $where          = 'log_activity.la_account_id =' . '"'.config_item('id').'" ';
        $return         = array();

        if ($keyword) {
            $where .= ' AND ( log_activity.la_user_name LIKE "%' . $keyword . '%" OR log_activity.la_phone_number LIKE "%' . $keyword . '%" OR log_activity.la_user_email LIKE "%' . $keyword . '%" OR log_activity.la_message LIKE "%' . $keyword . '%" ) ';
        }

        $usertype = ($usertype != 'all') ? $usertype : false;
        if ($usertype) {
            $where .= ' AND log_activity.la_usertype =' . $usertype;
        }

        if ($userid) {
            $where .= ' AND log_activity.la_user_id =' . $userid;
        }
        
        if($log_date_start || $log_date_end)
        {
            if($log_date_start && $log_date_end)
            {
                //$this->db->where("log_activity.la_created_date BETWEEN '$log_date_start' AND '$log_date_end'");
                $where .= ' AND log_activity.la_created_date BETWEEN ' . '"'.$log_date_start.'"' . ' AND ' . '"'.$log_date_end.'"';
            }
            else if($log_date_start)
            {
                //$this->db->where("log_activity.la_created_date >= '$log_date_start'");
                $where .= ' AND log_activity.la_created_date >=' . '"'.$log_date_start.'"';
            }
            else if($log_date_end)
            {
                //$this->db->where("log_activity.la_created_date <= '$log_date_end'");
                $where .= ' AND log_activity.la_created_date <=' . '"'.$log_date_end.'"';
            }
        }
         

        $limit_query = '';
        if (!$count) {
            if ($limit > 0) {
                $limit_query = 'LIMIT ' . $offset . ',' . $limit;
            }
        }

        $query = "SELECT " . $select . " FROM log_activity WHERE " . $where . " ORDER BY id desc " . $limit_query;
        if ($count) {
            $return = $this->db->query($query)->num_rows();
        } else {
            $return = $this->db->query($query)->result_array();
        }
        // if (!$count)
        // {
              //echo $this->db->last_query();die;
        // }
        return $return;
    }

    public function delete_log_activities()
    {
        $query = "DELETE FROM log_activity WHERE `la_created_date` < NOW() - INTERVAL 30 DAY";
        $result = $this->db->query($query);
        return $counts = $this->db->affected_rows();
    }

    public function save_course_performance($data, $param = array())
    {
        $id = isset($param['id']) ? $param['id'] : false;

        if ($id) {
            $this->db->update('course_perfomance', $data, array('id' => $id));
        } else {
            $this->db->insert('course_perfomance', $data);
        }
        return true;

    }
   

}

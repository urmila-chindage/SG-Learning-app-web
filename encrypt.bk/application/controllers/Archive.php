<?php
class Archive extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model(array('Archive_model', 'Routes_model'));
        $this->__lecture_type_array = array(  '1' => 'video',
                                              '2' => 'document',
                                              '3' => 'assesment',
                                              '4' => 'youtube',
                                              '5' => 'text',
                                              '6' => 'wikipedia',
                                              '7' => 'live',
                                              '8' => 'descriptive_test',
                                              '9' => 'recorded_videos'
                                       );
        $this->__files_to_delete = array();
    }
    
    function user()
    {
        //echo '<pre>fghfhgghgh'; 
        $users = $this->Archive_model->users();
        if(!empty($users))
        {
            foreach($users as $user)
            {
                $this->make_line('Listing objects for user ++++++++++++<b>'.$user['us_name'].'</b>++++++++++++++++');
                //print_r($user);

                $this->make_line('start of Listing assessment attampts reports  ');
                $sections = $this->Archive_model->assessment_report_user(array('user_id'=>$user['id']));
                //print_r($sections);

                $this->make_line('start of Listing assessment attampts ');
                $sections = $this->Archive_model->assessment_attempts(array('user_id'=>$user['id']));
                //print_r($sections);
                
                $this->make_line('start of Listing challanege attampts reports  ');
                $sections = $this->Archive_model->challenge_report_user(array('user_id'=>$user['id']));
                //print_r($sections);

                $this->make_line('start of Listing challenge attampts ');
                $sections = $this->Archive_model->challenge_zone_attempts(array('user_id'=>$user['id']));
                //print_r($sections);
                
                $this->make_line('start of Listing course preview time ');
                $sections = $this->Archive_model->course_preview_time(array('user_id'=>$user['id']));
                //print_r($sections);
                
                $this->make_line('start of Listing course subscription ');
                $sections = $this->Archive_model->course_subscription(array('user_id'=>$user['id']));
                //print_r($sections);
                
                $this->make_line('start of Listing course tutor ');
                $sections = $this->Archive_model->course_tutors(array('user_id'=>$user['id']));
                //print_r($sections);
                
                $this->make_line('start of Listing course wishlist ');
                $sections = $this->Archive_model->course_wishlist(array('user_id'=>$user['id']));
                //print_r($sections);
                
                $this->make_line('start of Listing descrptive_test_answers ');
                $sections = $this->Archive_model->get_descriptive_test_comments(array('user_id'=>$user['id']));
                //print_r($sections);
                
                $this->make_line('start of Listing descrptive_test_ USER ATTEMPTS ');
                $sections = $this->Archive_model->descriptive_user_answers(array('user_id'=>$user['id']));
                //print_r($sections);
                
                $this->make_line('start of Listing lecture log ');
                $sections = $this->Archive_model->lecture_logs(array('user_id'=>$user['id']));
                //print_r($sections);
                
                $this->make_line('start of Listing live lecture users ');
                $sections = $this->Archive_model->live_lecture_users(array('user_id'=>$user['id']));
                //print_r($sections);
                
                $this->make_line('start of Listing mentor ratings ');
                $sections = $this->Archive_model->mentor_ratings(array('user_id'=>$user['id']));
                //print_r($sections);
                
                $this->make_line('start of Listing recently viewd users ');
                $sections = $this->Archive_model->recently_view_users(array('user_id'=>$user['id']));
                //print_r($sections);
                
                $this->make_line('start of Listing tutor categpry ');
                $sections = $this->Archive_model->tutor_category(array('user_id'=>$user['id']));
                //print_r($sections);
                
                $this->make_line('start of Listing user generated test  ');
                $sections = $this->Archive_model->user_generated_assesment(array('user_id'=>$user['id']));
                //print_r($sections);

                $this->make_line('start of Listing user generated reports  ');
                $sections = $this->Archive_model->user_generated_report_user(array('user_id'=>$user['id']));
                //print_r($sections);

                $this->make_line('start of Listing user generated attampts ');
                $sections = $this->Archive_model->user_generated_attempts(array('user_id'=>$user['id']));
                //print_r($sections);
                
                $this->make_line('start of Listing user profile values ');
                $sections = $this->Archive_model->user_profile_values(array('user_id'=>$user['id']));
                //print_r($sections);
                
                $this->make_line('start of Listing chat_user_status ');
                $sections = $this->Archive_model->chat_user_status(array('user_id'=>$user['id']));
                //print_r($sections);
                
                $this->make_line('start of Listing user messages ');
                $sections = $this->Archive_model->chats(array('user_id'=>$user['id']));
                //print_r($sections);
            }
        }
    }
    
    function index()
    {
        $this->course();
        $this->user();
        $this->delete_pages();
        $this->delete_terms();
        $this->delete_notifications();
        $this->delete_challenges();
        $this->delete_expert_lectures();
        $this->delete_daily_news();
        
        $question_upload_path = scandir(question_upload_path());
        if(!empty($question_upload_path))
        {
            foreach($question_upload_path as $question_path)
            {
                if(!is_dir(question_upload_path().$question_path))
                {
                    $this->__files_to_delete[] = question_upload_path().$question_path;
                }
            }
        }
        //echo '<pre>'; print_r($question_upload_path);
        //echo '<pre>'; print_r($this->__files_to_delete);
        if(!empty($this->__files_to_delete))
        {
            foreach($this->__files_to_delete as $file)
            {
                if(file_exists($file))
                {
                    if(is_dir($file))
                    {
                        $this->deleteDir($file);
                    }
                    else
                    {
                        unlink($file);
                    }
                }
                //echo $file;
                //echo '<br />';
            }
        }
    }
    
    function deleteDir($path) {
        if (empty($path)) { 
            return false;
        }
        return is_file($path) ?
                @unlink($path) :
                array_map(array($this, 'deleteDir'), glob($path.'/*')) == @rmdir($path);
    }
    
    function course()
    {
        //deleted courses
        //delete course image
        //delete course slug
        //delete topics
        //find lectures
        //delete slug

        //echo '<pre>'; 
        $courses = $this->Archive_model->courses();
        
        if(!empty($courses))
        {
            foreach($courses as $course)
            {
                $this->make_line('Listing objects for course ++++++++++++<b>'.$course['cb_title'].'</b>++++++++++++++++');
                $this->__files_to_delete[] = course_upload_path(array('course_id' => $course['id'])).$course['cb_image'];
                //print_r($course);
                $this->Routes_model->delete($course['cb_route_id']);
                $this->make_line('start of Listing section ');
                $sections = $this->Archive_model->sections(array('course_id'=>$course['id'], 'delete' => true));
                //print_r($sections);

                $this->make_line('start of Listing discussion ');
                $sections = $this->Archive_model->course_discussions(array('course_id'=>$course['id'], 'delete' => true));
                //print_r($sections);
                
                $this->make_line('start of Listing discussion report ');
                $sections = $this->Archive_model->course_discussion_report(array('course_id'=>$course['id'], 'delete' => true));
                //print_r($sections);
                
                $this->make_line('start of Listing course preview times ');
                $sections = $this->Archive_model->course_preview_time(array('course_id'=>$course['id'], 'delete' => true));
                //print_r($sections);
                
                $this->make_line('start of Listing course ratings ');
                $course_ratings = $this->Archive_model->course_ratings(array('course_id'=>$course['id']));
                if(!empty($course_ratings))
                {
                    foreach($course_ratings as $course_rating)
                    {
                        if($course_rating['cc_user_image'] != 'default.jpg')
                        {
                            $this->__files_to_delete[] = user_upload_path().$course_rating['cc_user_image'];                        
                        }
                        //print_r($course_rating);                        
                    }
                }
                $this->Archive_model->course_ratings(array('course_id'=>$course['id'], 'delete' => true));


                
                $this->make_line('start of Listing course reviews ');
                $sections = $this->Archive_model->course_reviews(array('course_id'=>$course['id'], 'delete' => true));
                //print_r($sections);
                
                $this->make_line('start of Listing course subscription ');
                $sections = $this->Archive_model->course_subscription(array('course_id'=>$course['id'], 'delete' => true));
                //print_r($sections);
                
                $this->make_line('start of Listing course tutors ');
                $sections = $this->Archive_model->course_tutors(array('course_id'=>$course['id'], 'delete' => true));
                //print_r($sections);

                $this->make_line('start of Listing course wishlist ');
                $sections = $this->Archive_model->course_wishlist(array('course_id'=>$course['id'], 'delete' => true));
                //print_r($sections);
                
                $this->make_line('start of Listing recently vieec courses ');
                $sections = $this->Archive_model->recently_view_courses(array('course_id'=>$course['id'], 'delete' => true));
                //print_r($sections);

                
                $this->make_line('start of Listing lectues Logs ');
                $lecture_logs = $this->Archive_model->lecture_logs(array('course_id'=>$course['id'], 'delete' => true));
                //print_r($lecture_logs);

                $this->make_line('start of Listing lectues ');
                $lectures = $this->Archive_model->lectures(array('course_id'=>$course['id']));
                if($lectures)
                {
                    foreach($lectures as $lecture)
                    {                
                        $method       = $this->__lecture_type_array[$lecture['cl_lecture_type']];
                        $this->$method($lecture);
                    }
                }
                $this->Archive_model->lectures(array('course_id'=>$course['id'], 'delete' => true));
                
                // finally code to delete the couesew
                $this->Archive_model->delete_course($course['id']);
                //End
            }
        }
    }

    function video($lecture)
    {
        $this->__files_to_delete[] = video_upload_path(array('course_id' => $lecture['cl_course_id'])).$lecture['cl_filename'];
        $this->__files_to_delete[] = video_upload_path(array('course_id' => $lecture['cl_course_id'])).$lecture['cl_filename'].'_con.mp4';
        
        $video_extension = array('mp4', 'flv', 'avi', 'f4v');
        foreach($video_extension as $extension_key)
        {
            if(file_exists(video_upload_path(array('course_id' => $lecture['cl_course_id'])).$lecture['cl_filename'].'.'.$extension_key))
            {
                $this->__files_to_delete[] = video_upload_path(array('course_id' => $lecture['cl_course_id'])).$lecture['cl_filename'].'.'.$extension_key;            
            }
        }
        //echo '********************************<br />';
        //print_r($lecture);
    }
    function document($lecture)
    {
        $this->__files_to_delete[] = document_upload_path().$lecture['cl_filename'];
        
        $document_extension = array('doc', 'docx', 'xls', 'pdf', 'ppt', 'pptx');
        foreach($document_extension as $extension_key)
        {
            if(file_exists(document_upload_path().$lecture['cl_filename'].'.'.$extension_key))
            {
                $this->__files_to_delete[] = document_upload_path().$lecture['cl_filename'].'.'.$extension_key;            
            }
        }
        //echo '********************************<br />';        
        //print_r($lecture);
    }
    function assesment($lecture)
    {
        //echo '*****************assesment***************<br />';
        $data['assesment']           = $this->Archive_model->assesment(array('lecture_id' => $lecture['id'], 'course_id' => $lecture['cl_course_id']));
        $data['questions']           = $this->Archive_model->questions(array('assesment_id' => $data['assesment']['assesment_id'], 'not_deleted'=>'1'));
        $data['attempts']            = array();
        $attempts            = $this->Archive_model->assessment_attempts(array('assesment_id' => $data['assesment']['assesment_id'])); 
        if(!empty($attempts))
        {
            foreach($attempts as $attempt)
            {
                $attempt['report']  = $this->Archive_model->assessment_attempts_report(array('attempt_id' => $attempt['id']));
                $data['attempts'][$attempt['id']] = $attempt;
                $this->Archive_model->assessment_attempts_report(array('attempt_id' => $attempt['id'], 'delete' => true));
            }
        }
        $this->Archive_model->assessment_attempts(array('assesment_id' => $data['assesment']['assesment_id'], 'delete'=>true)); 
        //echo '<pre>';
        //print_r($data);

    }
    function youtube($lecture)
    {
        //print_r($lecture);
        
    }
    function text($lecture)
    {
        //print_r($lecture);
        
    }
    function wikipedia($lecture)
    {
        //print_r($lecture);
        
    }
    function live($lecture)
    {
        //echo '****************live****************<br />';
        $live_lecture                       = $this->Archive_model->live_lecture(array('lecture_id' => $lecture['id'], 'course_id' => $lecture['cl_course_id']));
        $live_lecture['live_recording']     = $this->Archive_model->get_live_recordings(array('live_id' => $live_lecture['live_lecture_id']));
        //echo '<pre>';//print_r($live_lecture);
    }
    function descriptive_test($lecture)
    {
        //echo '***************descriptive_test*****************<br />';
        $data['descriptive_test']  = $this->Archive_model->get_desctriptive_question(array('lecture_id' => $lecture['id']));
        $this->__files_to_delete[] = descriptive_question_path().$data['descriptive_test']['dt_file'];
        $this->__files_to_delete[] = descriptive_question_path(). substr($data['descriptive_test']['dt_file'], 0, -4);
        $this->Archive_model->get_desctriptive_question(array('lecture_id' => $lecture['id'], 'delete' => true));
        
        $descriptive_answers            = $this->Archive_model->descriptive_user_answers(array('lecture_id' => $lecture['id']));
        $data['descriptive_answers']    = array();
        if(!empty($descriptive_answers))
        {
            foreach($descriptive_answers as $descriptive_answer)
            {
                $descriptive_answer['comments'] = $this->Archive_model->get_descriptive_test_comments(array('attempt_id' => $descriptive_answer['id']));
                $data['descriptive_answers'][$descriptive_answer['id']] = $descriptive_answer;
            }
        }
        //echo '<pre>';
        //print_r($data);
    }
    function recorded_videos($lecture)
    {
        //echo '**************recorded_videos******************<br />';
        $data['recorded_details']  = $this->Archive_model->get_live_recordings(array('lecture_id' => $lecture['id']));
        //echo '<pre>';print_r($data);
    }
    
    function make_line($string)
    {
        //echo '======================================'.$string.'====================================<br />';
    }
    
    
    
    
    
    
    
    //==========================================================================================================================

    function delete_pages(){
        $this->make_line('start of Listing of pages ');
        $data = array();
        $data['pages'] = $this->Archive_model->cms_pages();
        foreach ($data['pages'] as $key => $page){
            //$data['pages'][$key]['route'] = $this->Archive_model->route_id(array('route_id'=>$page['p_route_id']));
           // $data['pages'][$key]['recent'] = $this->Archive_model->recent_pages(array('page_id'=>$page['id']));
            $this->Archive_model->route_id(array('route_id'=>$page['p_route_id'], 'delete' => true));
            $this->Archive_model->recent_pages(array('page_id'=>$page['id'], 'delete' => true));
        }
        $this->Archive_model->cms_pages(array('delete' => true));
        //echo '<pre>';print_r($data);
    }

    function delete_terms(){
        $this->make_line('start of Listing of terms ');
        $data = array();
        $data['terms'] = $this->Archive_model->terms();
        foreach ($data['terms'] as $key => $term){
            //$data['terms'][$key]['route'] = $this->Archive_model->route_id(array('route_id'=>$term['t_route_id']));
            //$data['terms'][$key]['recent'] = $this->Archive_model->recent_terms(array('term_id'=>$term['id']));
            $this->Archive_model->route_id(array('route_id'=>$term['t_route_id'], 'delete' => true));
            $this->Archive_model->recent_terms(array('term_id'=>$term['id'], 'delete' => true));
        }
        $this->Archive_model->terms(array('delete' => true));
        //echo '<pre>';print_r($data);
    }

    function delete_notifications(){
        $this->make_line('start of Listing of notification ');
        $data = array();
        $data['notifications'] = $this->Archive_model->cms_notifications();
        foreach ($data['notifications'] as $key => $notification){
            //$data['notifications'][$key]['route'] = $this->Archive_model->route_id(array('route_id'=>$notification['n_route_id']));
            $this->Archive_model->route_id(array('route_id'=>$notification['n_route_id'], 'delete' => true));
        }
        $this->Archive_model->cms_notifications(array('delete' => true));
        //echo '<pre>';print_r($data);
    }

    function delete_challenges(){
        $this->make_line('start of Listing of challenges ');
        $data = array();
        $data['challenge_zones'] = $this->Archive_model->challenge_zones();
        foreach ($data['challenge_zones'] as $key => $challenge){
            /*$data['challenge_zones'][$key]['questions'] = $this->Archive_model->challenge_questions(array('challenge_id'=>$challenge['id']));
            $data['challenge_zones'][$key]['attempts'] = $this->Archive_model->challenge_attempts(array('challenge_id'=>$challenge['id']));
            foreach ($data['challenge_zones'][$key]['attempts'] as $key1 => $attempt){
                $data['challenge_zones'][$key]['attempts'][$key1]['report'] = $this->Archive_model->challenge_attempt_report(array('attempt_id'=>$attempt['id']));
            }*/
            $this->Archive_model->challenge_questions(array('challenge_id'=>$challenge['id'], 'delete' => true));
            $data['challenge_zones'][$key]['attempts'] = $this->Archive_model->challenge_attempts(array('challenge_id'=>$challenge['id']));
            foreach ($data['challenge_zones'][$key]['attempts'] as $key1 => $attempt){
                $this->Archive_model->challenge_attempt_report(array('attempt_id'=>$attempt['id'], 'delete' => true));
            }
            $this->Archive_model->challenge_attempts(array('challenge_id'=>$challenge['id'], 'delete' => true));
            $this->Archive_model->challenge_zones(array('delete' => true));
        }
        //echo '<pre>';print_r($data);
    }

    function delete_expert_lectures(){
        $this->make_line('start of Listing of expert ;lectuerd ');
        $data = array();
        $data['expert_lectures'] = $this->Archive_model->expert_lectures(array('delete' => true));
        //echo '<pre>';//print_r($data);
    }

    function delete_daily_news(){
        $this->make_line('start of Listing of daily news ');
        $data = array();
        $data['daily_news'] = $this->Archive_model->daily_news(array('delete' => true));
        //echo '<pre>';
        //print_r($data);
        
    }
}
?>
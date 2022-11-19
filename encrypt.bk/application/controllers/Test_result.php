<?php

class Test_result extends CI_Controller
 {
	function index($id = false)
    {
        $this->load->model('Test_result_model');
        $id = $this->input->post('assessment_id');
        $id = base64_decode($id);
        //echo $id;
        $response                 = array();
        $assesment_id             = $id;
        $question                 = array(); 
        $question_data            = array();
        //$question                 = array();
        
        $total_correct_questions  = 0;
        $count                    = 0;
        $assesments               = $this->Test_result_model->get_assesment(array('assesment_id' => $assesment_id  ));

        $total_questions          = $this->Test_result_model->get_total_correct_questions($assesment_id );
      //  echo '<pre>'; print_r($total_questions); die;
        for($i=0; $i<count($total_questions); $i++) 
         {
           $question = $total_questions[$i]['q_question'];
           $question_data[$i]['qid']   = $total_questions[$i]['ar_question_id'];
           $question_data[$i]['total_correct']   = $total_questions[$i]['total_correct'];
           $question_data[$i]['question'] = base64_encode($question);
         }

        foreach ($total_questions as  $value)
         {
           $total_correct_questions =  (int)$total_correct_questions + (int)$value['total_correct']; 
           $count                   =  $count + 1;
         }
         
        $total_users                      = $this->Test_result_model->get_total_users($assesment_id ); 

        $response['data']['name']                 = $assesments['assesment_name'];
        $response['data']['correct_questions']    = $question_data;
        $response['data']['total_users_attended'] = $total_users;
        echo json_encode($response);  
    }

}
?>
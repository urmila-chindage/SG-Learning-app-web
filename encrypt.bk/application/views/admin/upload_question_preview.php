<?php
include_once('video_controller.php');
function toAlpha($number)
{
    $alpha = '';
    $alphabet = range('A','Z');
    //$number--;
    $count = count($alphabet);
    if($number <= $count)
        return $alphabet[$number-1];
    while($number > 0){
        $modulo     = ($number - 1) % $count;
        $alpha      = $alphabet[$modulo].$alpha;
        $number     = floor((($number - $modulo) / $count));
    }
    return $alpha;
}
//echo $url= $_SERVER['HTTP_REFERER'];
$key                       = 'qimpt'.$this->__loggedInUser['id'];
$questions_objects         = array();
$questions_objects['key']  = $key;
$uploaded_questions        = $this->memcache->get($questions_objects);
$questions                 = $uploaded_questions['questions'];
//echo '<pre>'; print_r($questions);die;
?>
<!DOCTYPE html>
<html>
    <!-- head start-->

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
        <title><?php echo isset($title) ? $title : config_item('site_name'); ?></title>
        <link rel="icon" href="<?php echo base_url('favicon.png') ?>">
        <!-- ADDING REDACTOR PLUGIN INSIDE -->
        <!-- ############################# --> <!-- START -->

        <!-- ############################# --> <!-- END -->
        <!-- ADDING REDACTOR PLUGIN INSIDE -->    
        <!-- Customized bootstrap css library -->
        <link rel="stylesheet" type="text/css" href="<?php echo assets_url() ?>css/bootstrap.css">
        <link rel="stylesheet" type="text/css" href="<?php echo assets_url() ?>css/adminstyle.css">
        <link rel="stylesheet" href="<?php echo assets_url() ?>css/autocomplete.css" />
        <style>
        #report-card .text-qus {font-weight: 400;} 
        #report-card{margin-bottom: 90px;}
        .preview-header{
            padding: 15px;
            border-bottom: 1px solid #e5e5e5;
            min-height: 16.42857143px;
            background: #7f8284;
            color: #fff;
        }
        .preview-footer{
            bottom: 0;
            width: 100%;
            padding: 10px 0px 10px 0px;
            text-align: center;
            background: #fff;
            box-shadow: 0px -2px 5px #e0e0e0;
            position: fixed;
        }
        .close {
            color: #fff!important;
            opacity: unset!important;
        }
        .report-card ol li{
            list-style: decimal !important;
        }
        
        .choice-footer-wrap ol li{
            list-style: decimal !important;
        }
        .defective-rows-count{color:#9c261e; cursor:pointer;}
        .defective-rows-count:hover{color:#5a110c;}
        .defect-row-id-col{vertical-align: middle !important;}
        .defect-row-reason-col{padding: 10px 20px !important;}
        
        </style>
    
    </head>
    <!-- head end-->
    <!-- body start-->
    <body>
      
        <div class="container">
          <!-- Trigger the modal with a button -->
  


    </div>
        <!-- Manin Iner container start -->
        <div class='bulder-content-inner add-question-block'>
           

                      <!-- Modal -->
                      
          <div class="" id="report-card" role="dialog">
            <div class="">
              <!-- Modal content-->
              <div class="">
                <div class="preview-header">
                  <button type="button" class="close"  onclick="location.href='<?php echo admin_url('generate_test') ?>'" >&times;</button>
                  <h4 style="margin:0" >
                  Questions Preview &nbsp;&nbsp;-&nbsp;&nbsp;<span id="question_preview_count"><strong>(<?php echo (!empty($questions))?count($questions):0; ?>)</strong></span>
                  <?php if(!empty($uploaded_questions['defective_rows'])): ?>
                  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <span id="defective_question_count_holder"></span>
                    <?php endif; ?>
                </h4>
                </div>
               
                <?php 
                     if(!empty($questions)){
                      $a=1;
                      foreach($questions as $question){ 
                          //print_r($question);
                            //echo $question['q_category'];
                            //die();
                        switch ($question['q_type']) {
                            case 1:
                                $q_type = 'Single Choice';
                                break;
                            case 2:
                                $q_type = 'Multiple Choice';
                                break;
                            case 3:
                                $q_type = 'Subjective Type';
                                break;
                            case 4:
                                $q_type = 'Fill in the blanks';
                                break;
                        }
                    ?>
                    <!-- card content -->
                    <div class="container-reduce-width" id="review-question_<?php echo $question['id']; ?>">
                        <div class="single-choice-wraper right" >
                            <div class="single-choice-header">
                                <div class="row">
                                    <div class="col-md-6">
                                        <span class="no-in-round "><?php echo $a; ?></span>
                                        <span class="single-choice-label"><?php echo $q_type; ?></span>
                                    </div>
                                    <div class="col-md-6 text-right">
                                        <button type="button" class="btn btn-danger" onclick="deletePreviewQuestion(<?php echo $a; ?>);">Delete</button>
                                    </div>
                                </div>
                               <div class="row">
                                    <div class="question-master-parent">
                                        <div class="col-md-6 no-padding">
                                            <?php
                                            $q_subjects  = $question['q_subjects'];
                                            $q_topics    = $question['q_topics'];
                                            ?>
                                            <div class="margin-top-bottom">
                                                <span class="text-blue">Subject : </span><span class="preview-select"><?php echo $question['q_subject']; ?></span>
                                                    <select class="form-control question-master-select" id="question_sub_<?php echo $a; ?>" onchange="topicGeneration('<?php echo $a; ?>','<?php echo $question['q_category']; ?>')">
                                                        <option value="0" >Choose Subject</option>
                                                        <?php foreach($q_subjects as $q_subject){ ?>
                                                        <option value="<?php echo $q_subject['id']; ?>" <?php if(strtolower($q_subject['qs_subject_name'])==strtolower($question['q_subject'])){ echo 'selected'; } ?>><?php echo $q_subject['qs_subject_name']; ?></option>
                                                        <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="single-choice-label  margin-top-bottom">
                                                <span class="text-blue">Topic : </span><span class="preview-select"><?php echo $question['q_topic']; ?></span>
                                                    <select class="form-control question-master-select" id="question_topic_<?php echo $a; ?>" >
                                                        <option value="0">Choose Topic</option>
                                                        <?php 
                                                        if($question['q_topic']!=''){
                                                            foreach($q_topics as $q_topic_obj){ ?>
                                                            <option value="<?php echo $q_topic_obj['id']; ?>" <?php if(strtolower($q_topic_obj['qt_topic_name'])==strtolower($question['q_topic'])){ echo 'selected="selected"'; } ?>><?php echo $q_topic_obj['qt_topic_name']; ?></option>
                                                        <?php
                                                            } 
                                                        } 
                                                        ?>
                                                    </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="question-master-parent">
                                    <span class="what-are-some-para"><p><?php echo $question['q_question'][1]; ?></p></span>
                                    <span class="question-wrap">
                                     <?php 
                                        if(($question['q_type']==1) || ($question['q_type']==2)){ 
                                       $q_options = $question['q_options']; 
                                       $b=1;
                                       foreach($q_options as $q_option){ 
                                        ?>
                                        <span class="series-of-question text-qus-padding-right">
                                            <span class="a-b-c"><?php echo toAlpha($b); ?>)</span>
                                            <span class="text-qus"><?php echo $q_option[1]; ?></span>
                                            <!-- <span class="tick"></span> -->
                                            <!--text-qus-->
                                        </span>
                                       <?php 
                                        $b++;
                                        }  
                                     }   
                                    ?>
                                   </span>
                                 </div>
                                <!--question-master-parent-->
                                <hr class="hr-alter">
                                <div class="choice-footer-wrap clearfix">
                                    <div class="choice-footer-wrap clearfix">
                                        <span class="your-answer-wrap-left">
                                                        <span class="your-answer-wrap-left-inside-text">Correct answer : &nbsp; <span class="text-blue"><?php echo $question['q_answer']; ?></span></span>
                                        
                                        <!-- <span class="right-text-green">Right</span> -->
                                        </span>
                                        <!--your-answer-wrap-->
                                        <span class="your-answer-wrap-right">
                                        <span class="small-device-border">
                                            <strong>+ve marks : <span class="green-status"><?php echo $question['q_positive_mark']; ?></span></strong>
                                            &nbsp;&nbsp;&nbsp;
                                            <strong>-ve marks : <span class="red-status"><?php echo $question['q_negative_mark']; ?></span></strong>
                                            &nbsp;&nbsp;&nbsp;
                                        </span>
                                        <!--your-answer-wrap-->
                                    </div>
                                </div>

                                <div class="choice-footer-wrap margin-top clearfix">
                                    <?php if($question['q_explanation'][1]!=''){ ?>
                                    <span class="answer-exp">Answer Explanation</span>
                                    <p><?php echo $question['q_explanation'][1]; ?></p>
                                    <?php } ?>
                                </div>
                                <div class="reveal-answer" id="reveal_answer_14"></div>
                            </div>
                            <!--single-choice-header-->
                            <div class="reveal-answer" id="reveal_answer_14"></div>
                        </div>
                                               
                    </div>
                    <?php
                     $a++;
                    } 
                 ?>
                    <!-- card content ends -->

                <!-- scroll top arrow -->
                <span id="scroll-top-arrow" onclick="topFunction();">
                    <svg enable-background="new 0 0 32 32" height="32px" id="Слой_1" version="1.1" viewBox="0 0 32 32" width="32px" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g id="Arrow_Up_Circle"><path d="M16,0C7.163,0,0,7.164,0,16c0,8.837,7.163,16,16,16c8.836,0,16-7.163,16-16C32,7.164,24.836,0,16,0z M16,30   C8.268,30,2,23.732,2,16S8.268,2,16,2c7.732,0,14,6.268,14,14S23.732,30,16,30z" fill="#121313"/><path d="M16.715,6.293C16.527,6.107,16.264,6,16,6c-0.263,0-0.526,0.108-0.714,0.293l-6.999,6.899   c-0.395,0.391-0.394,1.024,0,1.414c0.395,0.391,1.034,0.391,1.429,0l5.275-5.2V25c0,0.552,0.452,1,1.01,1   c0.558,0,1.01-0.448,1.01-1V9.407l5.275,5.2c0.394,0.39,1.034,0.39,1.428,0c0.394-0.391,0.394-1.024,0-1.414L16.715,6.293z" fill="#121313"/></g><g/><g/><g/><g/><g/><g/></svg>
                </span>
                
                <div class="preview-footer">
                    <button type="button" class="btn btn-danger" onclick="location.href='<?php echo (($uploaded_questions['assessment_id'])&&($uploaded_questions['lecture_id']!=''))? admin_url('test_manager/test_questions/'.base64_encode($uploaded_questions['lecture_id'])):admin_url('generate_test'); ?>'">Close</button>
                    <button type="button" class="btn btn-success" id="save-preview-questions" onclick="savePreviewQuestions()"  data-dismiss="modal">Save</button>
                </div>
                <?php } else { ?>
                <h3 style="text-align:center;margin-top:3em;">No questions found.</h3>
                <div class="preview-footer">
                <button type="button" class="btn btn-danger" onclick="location.href='<?php echo (($uploaded_questions['assessment_id'])&&($uploaded_questions['lecture_id']!=''))? admin_url('test_manager/test_questions/'.base64_encode($uploaded_questions['lecture_id'])):admin_url('generate_test'); ?>'">Close</button>
                </div>
                <?php  } ?>
              </div>
            </div>
          </div>
       
            <!-- modal ends -->
        </div>
        <!-- Manin Iner container end -->

    </body>
    <!-- body end-->

<?php include_once "common_modals.php" ?>
<script type="text/javascript" src="<?php echo assets_url() ?>js/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/layout.min.js"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/system.js"></script>
<script>
window.onscroll = function() {scrollFunction()};

function scrollFunction() {
if (document.body.scrollTop > 40 || document.documentElement.scrollTop > 40) {
    document.getElementById("scroll-top-arrow").style.display = "block";
} 
else {
    document.getElementById("scroll-top-arrow").style.display = "none";
 }
}

$('#scroll-top-arrow').click(function(){
    $('html, body').animate({scrollTop:0}, 'slow');
});



var admin_url ='<?php echo admin_url(); ?>';
var asset_url ='<?php echo assets_url() ?>';
var __uploaded_questions ='';
var __assessment_id      = '<?php echo $uploaded_questions['assessment_id']; ?>';
$(document).ready(function(){
__questions_count = '<?php echo count($questions); ?>';
});
function deletePreviewQuestion(question_id){
     question_id = typeof question_id != 'undefined' ? question_id : '';
    var messageObject = {
    'body':'Are you sure to delete this question?',
    'button_yes':'CONTINUE', 
    'button_no':'CANCEL',
    'continue_params':{'question_id':question_id}
};
callback_warning_modal(messageObject,deletePreviewQuestions);
}

function deletePreviewQuestions(param){
    var question_id = param.data.question_id;
        //$('#test_pop_continue').html('Deleting...');
        $.ajax({
            url: admin_url+'generate_test/delete_preview_questions',
            type: "POST",
            data:{ "is_ajax":true,"question":question_id},
            success: function(response){
                $('#common_message_advanced').hide();
                window.location.reload();
            }
        });
}
function topicGeneration(key,category){
    var subject_id = $("#question_sub_"+key+" option:selected").val();
    $.ajax({
            url: admin_url + 'question_manager/get_topics',
            type: "POST",
            data: {"is_ajax": true, 'course_category': category, 'question_subject': subject_id},
            success: function (response) {
                var data = $.parseJSON(response);
                //console.log(data);
                if(data['error']==false)
                {
                    $("#question_topic_"+key).html(" ");
                    var renderTopicHtml = '';
                    renderTopicHtml = '<option value="">Choose Topic</option>';

                    if(data['topic'].length!=0){
                        for (i = 0; i < data['topic'].length; i++) { 
                        var topic_data    = data['topic'][i];
                           renderTopicHtml            += '<option value="'+topic_data['id']+'" >'+topic_data['qt_topic_name']+'</option>';
                        }  
                    }
                    $("#question_topic_"+key).prepend(renderTopicHtml);
                   

                }
            }
        });
}
function savePreviewQuestions(){
    var selectedValues              = new Array();
    
    //var uploadedQuestions          = $.parseJSON(__uploaded_questions);
    for (var i = 1; i <= __questions_count; i++) {
        var data            = {};
        //alert($("#question_sub_1 option:selected").val());
        data.i              = i;
        data.subject_id     = $("#question_sub_"+i+" option:selected").val();
        data.topic_id       = $("#question_topic_"+i+" option:selected").val();
        selectedValues[i]   = data;
        //console.log(data);
    }
    //console.log(selectedValues)
    var selectedString = JSON.stringify(selectedValues);
    $("#save-preview-questions").html('Saving<img src="'+asset_url+'images/loader.svg" width="25">');
    $.ajax({
        url: admin_url+'generate_test/confirm_import_questions',
        type: "POST",
        data:{ "is_ajax":true, "selectedValues": selectedString},
        success: function(response){
            var data = $.parseJSON(response);
            if(data['success']=='true')
            {
                $("#save-preview-questions").html('Save');
                if(__assessment_id==0)
                {
                window.location.href=admin_url+'generate_test';
                } else 
                {
                window.location.href=admin_url+'test_manager/test_questions/'+data['lecture_id'];
                }
            }
            else 
            {
                $("#save-preview-questions").html('Save');
                var messageObject = {
                    'body':data['message'],
                    'button_yes':'OK', 
                    'button_no':'CANCEL'
                };  
                callback_warning_modal(messageObject);
            }
        }
    });
}
</script>

<?php if(!empty($uploaded_questions['defective_rows'])): ?>

<script>
const __defectRows              = $.parseJSON(atob('<?php echo base64_encode(json_encode($uploaded_questions['defective_rows'])); ?>'));
var __defectiveQuestionLoaded   = false;
var __defectiveQuestionsHtml    = '';
var __defectReasonColumnHtml    = '';
function renderDefectiveQuestion() {
    $('#render_defective_question_modal').modal('show');
    if(__defectiveQuestionLoaded == false) {
        $('#defect_reason_html').html('<tr><th class="text-center defect-row-id-col" width="80" scope="row"></th><td class="defect-row-reason-col">Loading...</th>');
        if(Object.keys(__defectRows).length > 0 ) {
            $.each(__defectRows, function(rowKey, row) {
                __defectReasonColumnHtml    = '';
                __defectiveQuestionsHtml += '<tr>';
                __defectiveQuestionsHtml += '    <th class="text-center defect-row-id-col" width="80" scope="row">'+rowKey+'</th>';
                if(Object.keys(row).length > 0 ) {
                    $.each(row, function(defectKey, defect) {
                        __defectReasonColumnHtml += defect+'<br />';
                    });
                }
                __defectiveQuestionsHtml += '    <td class="defect-row-reason-col">'+__defectReasonColumnHtml+'</td>';
                __defectiveQuestionsHtml += '</tr>';
            });
        }
        __defectiveQuestionLoaded = true;
        $('#defect_reason_html').html(__defectiveQuestionsHtml);
    }
}
$(document).ready(function(){
    $('#defective_question_count_holder').html('<span class="defective-rows-count" onclick="renderDefectiveQuestion()">Invalid Questions &nbsp;&nbsp;-&nbsp;&nbsp;<span id="invalid_question_preview_count"><strong>(<?php echo count($uploaded_questions['defective_rows']); ?>)</strong></span></span>');
});
</script>

<div class="modal fade alert-modal " data-backdrop="static" data-keyboard="false" id="render_defective_question_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Listing Defective Questions</h4>
            </div>
            <div class="modal-body" style="max-height: calc(100vh - 115px);overflow-y: auto;background: #fff;">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th scope="col" class="text-center" width="80">ROW ID</th>
                            <th scope="col">REASON</th>                        
                        </tr>
                    </thead>
                    <tbody id="defect_reason_html">
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
</html>

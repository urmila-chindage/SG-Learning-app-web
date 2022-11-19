<?php include_once "header.php"; ?>  

<style>
    .question-bank-bulk{
        padding-bottom: 0px !important;
    }
    .question-count{
        overflow: visible !important;
    }
   
    iframe{ border:none;}
</style>
<?php //include_once "cms_tab.php"; ?>
<!-- <section class="courses-tab base-cont-top"> 
    <ol class="nav nav-tabs offa-tab">
        <li class="active">
            <a href="javascript:void(0)"> Question Bank</a>
            <span class="active-arrow" style="background: rgb(255, 255, 255) none repeat scroll 0% 0%;"></span>
        </li>
    </ol>
</section> -->


<!-- SIDEBAR RIGHT SIDE FIXED --> <!-- START -->
<div class="right-wrap base-cont-top container-fluid pull-right" style="top:95px!important;">
    <?php 
    $edit_permission    = (in_array('3', $this->__permission))?'1':'0';
    $delete_permission  = (in_array('4', $this->__permission))?'1':'0';
    if(isset($lecture_id) && $lecture_id > 0): ?>

        <?php if(in_array('2', $this->__permission)):?>
        <a href="javascript:void(0)" id="import_question_confirmed" class="btn btn-big btn-green disabled selected full-width-btn" onclick="importQuestionToAssessment()">
            Import Questions
        </a>
        <a href="<?php echo admin_url('test_manager/test_questions/'.base64_encode($lecture_id)) ?>" class="btn btn-big btn-blue full-width-btn">
            Cancel
            <ripples></ripples>
        </a>
        <?php endif; ?>
    <?php else: ?>
        <?php if(in_array('2', $this->__permission)):?>
        <a href="<?php echo admin_url('generate_test/question/0') ?>" class="btn btn-big btn-blue selected full-width-btn">
            <?php echo lang('add_question') ?>
        </a>
        <a href="javascript:void(0)" class="btn btn-big btn-green selected full-width-btn" id="generate_upload_question" data-toggle="modal" data-target="#addquestion">
            <?php echo lang('upload_question') ?>
        </a>
        <?php endif; ?>
    <?php endif; ?>

       
    <!--  Adding list group  --> <!-- END  -->
</div>
<!-- SIDEBAR RIGHT SIDE FIXED --> <!-- END -->



<section class="content-wrap base-cont-top">

    <!-- Nav section inside this wrap  --> <!-- START -->
    <!-- =========================== -->
<div class="container-fluid nav-content nav-course-content add-question-block" style="width:100% !important;top: 0px !important;height: 50px;">
    <div class="col-sm-12 bottom-line question-head">
        <h3 class="question-title">Import Questions From Bank</h3>
        <span class="cb-close-qstn" style="right: 30px;"><i class="icon icon-cancel-1" onclick="location.href='<?php echo admin_url('test_manager/test_questions/'.base64_encode($lecture_id)) ?>'"></i></span>
    </div>

        <div class="row">
            <div class="rTable content-nav-tbl" style=" position: relative; top: 50px;    background: #fff;">
                <div class="rTableRow">

                    <div class="rTableCell dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" id="filter_dropdown_text_difficulty"> All Questions <span class="caret"></span></a>
                        <ul class="dropdown-menu white inner-scroll">
                            <li><a href="javascript:void(0)" id="filer_dropdown_list_all" onclick="filter_generate_test_by('all')">All Questions</a></li>
                            <li><a href="javascript:void(0)" id="filer_dropdown_list_easy" onclick="filter_generate_test_by('easy')"><?php echo lang('easy') ?></a></li>
                            <li><a href="javascript:void(0)" id="filer_dropdown_list_medium" onclick="filter_generate_test_by('medium')"><?php echo lang('medium') ?></a></li>
                            <li><a href="javascript:void(0)" id="filer_dropdown_list_hard" onclick="filter_generate_test_by('hard')"><?php echo lang('hard') ?></a></li>
                        </ul>
                    </div>

                    <div class="rTableCell dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" id="filter_dropdown_text_type"> All Types <span class="caret"></span></a>
                        <ul class="dropdown-menu white inner-scroll">
                            <li><a href="javascript:void(0)" id="filer_dropdown_list_type_all" onclick="filter_generate_test_by_type('all')">All Types</a></li>
                            <li><a href="javascript:void(0)" id="filer_dropdown_list_type_single" onclick="filter_generate_test_by_type('single')">Single choice</a></li>
                            <li><a href="javascript:void(0)" id="filer_dropdown_list_type_multiple" onclick="filter_generate_test_by_type('multiple')">Multiple choice</a></li>
                            <li><a href="javascript:void(0)" id="filer_dropdown_list_type_subjective" onclick="filter_generate_test_by_type('subjective')">Subjective type</a></li>
                            <li><a href="javascript:void(0)" id="filer_dropdown_list_type_blanks" onclick="filter_generate_test_by_type('blanks')">Fill in the blanks</a></li>
                        </ul>
                    </div>

                    <div class="rTableCell dropdown">

                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" id="filter_dropdown_text_category"> All Categories <span class="caret"></span></a>
                        <ul class="dropdown-menu white inner-scroll">
                            <li><a href="javascript:void(0)" id="dropdown_list_all" onclick="filter_category('all')">All Categories</a></li>
                            
                            <?php if(!empty($q_parent_category)): ?>
                            <?php foreach($q_parent_category as $category): ?>
                                <?php if(strip_tags($category['ct_name'])): ?>
                                    <li><a href="javascript:void(0)" id="dropdown_list_<?php echo $category['id'] ?>" onclick="filter_category(<?php echo $category['id'] ?>)"><?php echo $category['ct_name'] ?></a></li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </ul>

                        
                    </div>
                    <div class="rTableCell dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" id="filter_dropdown_text_subjects">All Subjects <span class="caret"></span></a>
                        <ul class="dropdown-menu white inner-scroll" id="filter_dropdown_text_subjects_ul">
                            <li style="visibility:hidden;"><a href="javascript:void(0)" id="dropdown_subject_list_0" ></a></li>
                        </ul>
                    </div>
                    <div class="rTableCell dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" id="filter_dropdown_text_topics">All Topics <span class="caret"></span></a>
                        <ul class="dropdown-menu white inner-scroll" id="filter_dropdown_text_topics_ul">
                            <li style="visibility:hidden;"><a href="javascript:void(0)" id="dropdown_topic_list_0" ></a></li>
                        </ul>
                    </div>

                    <div class="rTableCell">
                        <div class="input-group">
                            <input type="text" class="form-control srch_txt" id="generate_questions_keyword" placeholder="Search" />
                            <a class="input-group-addon" id="basic-addon2">
                                <i class="icon icon-search"> </i>
                            </a>
                        </div> 
                    </div>
                    
                    <div class="rTableCell" >
                        <!-- lecture-control start -->
                        <div class="btn-group lecture-control btn-right-align" id="generate_test_bulk_action" style="margin-top: 0px; display:none;" id="course_bulk">
                            <span class="dropdown-tigger" data-toggle="dropdown">
                                <span class='label-text'>
                                   Bulk Action  <!-- <span class="icon icon-down-arrow"></span> -->
                                </span>
                                <span class="tilder"></span>
                            </span>
                            <ul class="dropdown-menu pull-right" role="menu">
                                <li><a href="javascript:void(0)" class="list-disabled list-button" onclick="deleteQuestionBulk()">Delete</a></li>

                            </ul>
                        </div>
                        <!-- lecture-control end -->
                    </div>
                </div>
            </div>

        </div>
    </div>
    
    <!-- =========================== -->
    <!-- Nav section inside this wrap  --> <!-- END -->


    <!-- LEFT CONTENT --> <!-- STARTS -->
    <!-- ===========================  -->

    <div class='bulder-content-inner' style="position: relative;top:-20px;">


    <div class="col-sm-12 builder-right-inner" style="background: none; padding: 0px 15px;"> <!-- right side bar section -->
        <?php include 'messages.php'; ?>
        
       <!-- top Header with drop down and action buttons -->
        <div  style="margin:0 !important">
             <span class="pull-left" style="padding: 20px 7px;">
                <label>
                            <input type="checkbox" class="rdobtn question-option-input" id="selectall">
                            <span class="select-span" id ="sel_all" >Select All</span>
                </label>
                        </span>

                        
                                  
                <div class="pull-right">
                    <!-- Header left items -->
                    <h4 class="right-top-header question-count">
                        <?php 
                        $question_html  = '';
                        $question_html .= sizeof($questions).' / '.$total_questions;
                        $question_html .= ($total_questions>1)?' Questions':' Question';
                        echo $question_html;
                        $remaining_question = $total_questions - sizeof($questions);
                        $remaining_question = ($remaining_question>0)?'('.$remaining_question.')':'';
                        ?>
                    </h4>
                </div>
            <!-- !.Header left items -->

            
        </div>
        <!-- !.top Header with drop down and action buttons --> 
        
        <!-- Preivew of  test content will show here -->
        <div class=" test-content generate-test-wrapper" id="generate_test_wrapper" style="clear: both;">

            <?php 
            $question_types = array( '1' =>  'Single Choice', '2' =>  'Multiple Choice', '3' =>  'Subjective','4' =>  'Fill in the blanks' );
            ?>
            <!-- test folder root or parent section begins here -->
            <?php if(!empty($questions)): 
                $sl_no = 1;?>
            
            <?php foreach ($questions as $question) :?>
            <div class="default-view-txt m0 test-folder" style="float: none; padding: 7px;"  id="question_wrapper_<?php echo $question['id'] ?>"><label><div>
                <input type="checkbox" class="import-questions" value="<?php echo $question['id'] ?>">
                <span class="question-sl-no"><?php //echo $sl_no++ ?> <!--.--> <b>#<?php echo $question['id'] ?> </b></span>
                <span class="question-text">
                    <?php 
                    $question['q_question'] = json_decode($question['q_question']);
                    if(!(json_last_error() == JSON_ERROR_NONE))
                    {
                        $question['q_question'] = '';
                    }
                    else
                    {
                        $active_web_language = '1';
                        if(isset($question['q_question']->$active_web_language))
                        {
                            $question['q_question'] = stripslashes($question['q_question']->$active_web_language);
                        }
                        else
                        {
                            $question['q_question'] = '';
                        }
                    }
                    $question_stripped = strip_tags($question['q_question']);
                    echo (strlen($question_stripped) > 100)?(substr($question_stripped, 0, 97).'...'):$question_stripped;
                    ?>
                    </span>
                <span class="question-type"><?php echo $question_types[$question['q_type']]; ?></span>
           
             </div></label></div>
            <?php endforeach; ?>
            <?php endif; ?>
            <!-- !.test folder root or parent section begins here -->
        </div>
        <div <?php echo ((!$show_load_button)?'style="display:none;"':'') ?> class="default-view-txt m0 mb10 text-center " onclick="getQuestions()">
            <a href="javascript:void(0)" class="btn btn-green" id="load_more_question" data-toggle="modal" data-target="">
                Load More Question <?php echo $remaining_question ?><ripples></ripples>
            </a>
        </div>
            
    </div><!-- right side bar section -->

</div>
    <!-- ==========================  -->
    <!--  LEFT CONTENT--> <!-- ENDS -->


</section>


<!-- Basic All Javascript -->
<script type="text/javascript" src="<?php echo assets_url() ?>js/import_question_bank.js"></script>
<script>
var __assessmentId      = '<?php echo $assessment_id ?>';
var __lectureId         = '<?php echo $lecture_id ?>';
var __edit_permission   = '<?php echo $edit_permission ?>';
var __delete_permission = '<?php echo $delete_permission ?>';
var __deleteQuestionIds = {};
var __questionIdChecked = 0;
var __importQuestionIds = {};
var __questionIdChecked = 0;

$('#selectall').change(function(){
    if($(this).is(":checked")){
        $('.import-questions').each(function() {
             __questionIdChecked = $(this).val();
            $(this).prop('checked', true);
            if($(this).prop('checked') == true)
            {
                __importQuestionIds[__questionIdChecked] = __questionIdChecked;
            }
        });
    }else{
        $('.import-questions').each(function(){
            $(this).prop('checked', false);
        });
        __importQuestionIds = {};
    }  
    if(Object.keys(__importQuestionIds).length > 0 )
    {
        var questionCountHtml = Object.keys(__importQuestionIds).length;
        $('#import_question_confirmed').removeClass('disabled').html('Import Questions ('+questionCountHtml+')');
        $('#sel_all').html('Select all ('+questionCountHtml+')');
    }
    else
    {
        $('#import_question_confirmed').addClass('disabled').html('Import Questions');
        $('#sel_all').html('Select all');        
    }      
});

$(document).on('change', '.import-questions', function(){
    __questionIdChecked = $(this).val();
    if($(this).prop('checked') == true)
    {
        __importQuestionIds[__questionIdChecked] = __questionIdChecked;
    }
    else
    {
        delete __importQuestionIds[__questionIdChecked];
    }
    if(Object.keys(__importQuestionIds).length > 0 )
    {
        var questionCountHtml = Object.keys(__importQuestionIds).length;
        $('#import_question_confirmed').removeClass('disabled').html('Import Questions ('+questionCountHtml+')');
        $('#sel_all').html('Select all ('+questionCountHtml+')');
    }
    else
    {
        $('#import_question_confirmed').addClass('disabled').html('Import Questions');
        $('#sel_all').html('Select all');           
    }
});

var __importQuestionInProgress = false;
function importQuestionToAssessment()
{
    $.ajax({
        url: admin_url+'generate_test/import_question',
        type: "POST",
        data:{"is_ajax":true, "question_ids":JSON.stringify(__importQuestionIds), 'assessment_id':__assessmentId, 'lectureId':__lectureId},
        success: function(response) {
            var data = $.parseJSON(response);
            if(data['error'] == false)
            {
                location.href = admin_url+'test_manager/test_questions/'+btoa(__lectureId);
            }
            else
            {
                lauch_common_message('Something went Wrong' , 'Please try to import question again!!');
            }
            __importQuestionInProgress = false;
        }
    });
}


</script>
<!-- END -->

<?php include_once 'footer.php'; ?>
<!-- Modal pop up contents:: Delete Section popup-->
    <div class="modal fade alert-modal-new" data-backdrop="static" data-keyboard="false" id="test_basic_modal" role="dialog" 
    style="z-index: 999999;">
        <div class="modal-dialog modal-small" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="form-group">
                        <b id="test_pop_title"></b>
                        <p class="m0" id="test_pop_desc"></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-red" id="test_pop_cancel" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-green" id="test_pop_continue">Continue</button>
                </div>
            </div>
        </div>
    </div>
<div class="container">
          <!-- Trigger the modal with a button -->
  

          <!-- Modal -->
          <div class="modal fade" id="report-card" role="dialog">
            <div class="modal-dialog">
              <!-- Modal content-->
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" onclick="clearPreviewQuestions()" data-dismiss="modal">&times;</button>
                  <h4 class="modal-title" >Question Preview &nbsp;&nbsp;-&nbsp;&nbsp;(<span id="question_preview_count"></span> Questions)</h4>
                </div>
                <div class="modal-body"> 
                    <!-- card content -->
                    <div class="container-reduce-width" id="review-question">
                        <div class="single-choice-wraper right" >
                            <div class="single-choice-header">
                                <div class="row">
                                    <div class="col-md-6">
                                        <span class="no-in-round ">1</span>
                                        <span class="single-choice-label">Single choice</span>
                                    </div>
                                    <div class="col-md-6 text-right">
                                        <button type="button" class="btn btn-danger" data-dismiss="modal">Delete</button>
                                    </div>
                                </div>
                                <div class="question-master-parent">
                                    <div class="col-md-6 no-padding">
                                        <div class="margin-top-bottom"><span class="text-blue">Subject : </span> Subject name</div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="single-choice-label  margin-top-bottom"><span class="text-blue">Topic : </span> Topic name</div>
                                    </div>
                                </div>
                                
                                <div class="question-master-parent">
                                    <span class="what-are-some-para"><p>From what location are the 1st computer instructions available on boot up?</p></span>
                                    <span class="question-wrap">
                                        <span class="series-of-question text-qus-padding-right">
                                            <span class="a-b-c">a)</span>
                                            <span class="text-qus"><p>ROM BIOS</p></span>
                                            <!-- <span class="tick"></span> -->
                                            <!--text-qus-->
                                        </span>
                                    <!--series-of-question-->
                                    <span class="series-of-question text-qus-padding-left">
                                                    <span class="a-b-c">b)</span>
                                    <span class="text-qus"><p>CPU</p></span>
                                    <!--text-qus-->
                                    </span>
                                    <!--series-of-question-->
                                    </span><span class="question-wrap">
                                              
                                                <span class="series-of-question text-qus-padding-right">
                                                    <span class="a-b-c">c)</span>
                                    <span class="text-qus"><table><tbody><tr><td>boot.ini</td></tr><tr></tr></tbody></table></span>
                                    <!--text-qus-->
                                    </span>
                                    <!--series-of-question-->
                                    <span class="series-of-question text-qus-padding-left">
                                                    <span class="a-b-c">d)</span>
                                    <span class="text-qus"><p>CONFIG.SYS</p></span>
                                    <!--text-qus-->
                                    </span>
                                    <!--series-of-question-->
                                    </span><span class="question-wrap">
                                              
                                                <span class="series-of-question text-qus-padding-right">
                                                    <span class="a-b-c">e)</span>
                                    <span class="text-qus"><p>None of the above</p></span>
                                    <!--text-qus-->
                                    </span>
                                    <!--series-of-question-->
                                    </span>
                                    <!--question-wrap-->
                                </div>
                                <!--question-master-parent-->
                                <hr class="hr-alter">
                                <div class="choice-footer-wrap clearfix">
                                    <div class="choice-footer-wrap clearfix">
                                        <span class="your-answer-wrap-left">
                                                        <span class="your-answer-wrap-left-inside-text">Correct answer : &nbsp; <span class="text-blue"> A</span></span>
                                        
                                        <!-- <span class="right-text-green">Right</span> -->
                                        </span>
                                        <!--your-answer-wrap-->
                                        <span class="your-answer-wrap-right">
                                        <span class="small-device-border">
                                            <strong>+ve marks : <span class="green-status">5</span></strong>
                                            &nbsp;&nbsp;&nbsp;
                                            <strong>-ve marks : <span class="red-status">-1</span></strong>
                                            &nbsp;&nbsp;&nbsp;
                                        </span>
                                        <!--your-answer-wrap-->
                                    </div>
                                </div>

                                <div class="choice-footer-wrap margin-top clearfix">
                                    <span class="answer-exp">Answer Explanation</span>
                                    <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500.
                                    Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p>
                                </div>
                                <div class="reveal-answer" id="reveal_answer_14"></div>
                            </div>
                            <!--single-choice-header-->
                            <div class="reveal-answer" id="reveal_answer_14"></div>
                        </div>
                                               
                    </div>
                    <!-- card content ends -->
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-danger" onclick="clearPreviewQuestions()" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" onclick="savePreviewQuestions()"  data-dismiss="modal">Save</button>
                </div>
              </div>
            </div>
          </div>
    </div>
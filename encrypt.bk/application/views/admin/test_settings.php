<?php include_once 'coursebuilder/lecture_header.php';?>
<!-- ADDING REDACTOR PLUGIN INSIDE -->
<!-- ############################# --> <!-- START -->
    <link rel="stylesheet" href="<?php echo assets_url() ?>css/redactor/css/redactor.css" />
    <link rel="stylesheet" href="<?php echo assets_url() ?>css/redactor/css/alignment.css" />

<!-- ############################# --> <!-- END -->
<link rel="stylesheet" href="<?php echo assets_url() ?>css/datepicker.min.css">
<link rel="stylesheet" href="<?php echo assets_url() ?>css/timepicker.css">

<link href="<?php echo assets_url() ?>css/bootstrap-tagsinput.css" rel="stylesheet" type="text/css" />
<!--Tag input js-->
<script src="<?php echo assets_url() ?>js/bootstrap-tagsinput.js" type="text/javascript"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/datepicker.js"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/datepicker.en.js"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/jquery.timepicker.js"></script>
<!-- MAIN TAB --> <!-- STARTS -->
<?php include_once('test_header.php'); ?>
<div class="right-wrap small-width base-cont-top-heading container-fluid pull-right rightwrap-top-update">
    <br/>
    <div class="list-group test-listings">
      <a href="javascript:void(0)" class="list-group-item active">
        <span class="font15">Instructions</span>
      </a>
      <a href="javascript:void(0)" class="list-group-item link-style"><span class="green-span"><i class="icon icon-ok-circled"></i></span> <span class="listing-text">Advanced settings will facilitate easy changes in the quiz arrangements, time, options and reports.</span></a>
      <a href="javascript:void(0)" class="list-group-item link-style"><span class="green-span"><i class="icon icon-ok-circled"></i></span>          <span class="listing-text">To group the questions according to their subject enable <b>Group question subject wise</b> settings.</span></a>
      <a href="javascript:void(0)" class="list-group-item link-style"><span class="green-span"><i class="icon icon-ok-circled"></i></span>          <span class="listing-text">To show the marks of each question to the student enable <b>show marks for quiz</b> settings.</span></a>
    </div>            
    <!--  Adding list group  --> <!-- END  -->

</div>
<section class="content-wrap small-width base-cont-top-heading content-top-update">
    <!-- LEFT CONTENT --> <!-- STARTS -->

    <!-- ===========================  -->
    <div class="left-wrap col-sm-12 pad0">
        <!-- Nav section inside this wrap  --> <!-- START -->
        <!-- =========================== -->
        <div class="container-fluid course-create-wrap">

            <div class="row-fluid course-cont">
                <div class="col-sm-12">
                    <div class="form-horizontal" id="course_form">
                        <?php //include_once('messages.php') ?>
                        <form class="form-horizontal" id="save_test_basics" method="post" action="<?php echo admin_url('test_manager/test_settings'.'/'.base64_encode($test['id'])); ?>">
                            
                            <div class="each-steps test-step-two" id="step-two">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-sm-12 arrangement-grouping">
                                            <div class="arrangement-panel">Arrangement &amp; Grouping</div>
                                            <div class="addtest-container">
                                                <div class="addtest-checkbox">
                                                    <span class="cb-radio checkbox-btn">
                                                    <input <?php echo $test['a_qgrouping']==1?'checked="checked"':''; ?> id="test_question_grouping" name="test_question_grouping" value="1" type="checkbox" class="rdobtn question-option-input">
                                                        <label class="rdoinr rdc">
                                                            <span class="inrrclr"></span>
                                                        </label>                                        
                                                    </span>   
                                                    <span class="download-txt">Group questions subjectwise</span>
                                                </div>
                                            
                                                <div class="addtest-checkbox">
                                                    <span class="cb-radio checkbox-btn">
                                                    <input <?php echo $test['a_qshuffling']==1?'checked="checked"':''; ?> name="test_question_shuffling" id="test_question_shuffling" value="1" type="checkbox"  class="rdobtn question-option-input">
                                                        <label class="rdoinr rdc">
                                                            <span class="inrrclr"></span>
                                                        </label>                                        
                                                    </span>   
                                                    <span class="download-txt">Shuffle questions</span>  
                                                </div>    
                                            </div>    
                                        </div>
                                    </div>
                                    

                                    <div class="row">
                                        <div class="col-sm-12 arrangement-grouping">
                                            <div class="arrangement-panel">Test Options</div>
                                            <div class="addtest-container">
                                                <div class="addtest-checkbox">
                                                    <span class="cb-radio checkbox-btn">
                                                    <input <?php echo $test['a_show_mark']==1?'checked="checked"':''; ?> id="test_show_mark" name="test_show_mark" value="1" type="checkbox" class="rdobtn question-option-input">
                                                        <label class="rdoinr rdc">
                                                            <span class="inrrclr"></span>
                                                        </label>                                        
                                                    </span>   
                                                    <span class="download-txt">Show marks carrying for each question</span>
                                                </div>
                                            
                                                <div class="addtest-checkbox">
                                                    <span class="cb-radio checkbox-btn">
                                                    <input <?php echo $test['a_limit_navigation']==1?'checked="checked"':''; ?> name="test_question_navigate" id="test_question_navigate" value="1" type="checkbox"  class="rdobtn question-option-input">
                                                        <label class="rdoinr rdc">
                                                            <span class="inrrclr"></span>
                                                        </label>                                        
                                                    </span>   
                                                    <span class="download-txt">Restrict the user navigation in question palette</span>
                                                </div>

                                                <div class="addtest-checkbox">
                                                    <span class="cb-radio checkbox-btn">
                                                    <input <?php echo $test['a_attend_all']==1?'checked="checked"':''; ?> name="test_attend_all" id="test_attend_all" value="1" type="checkbox" class="rdobtn question-option-input">
                                                        <label class="rdoinr rdc">
                                                            <span class="inrrclr"></span>
                                                        </label>                                        
                                                    </span>   
                                                    <span class="download-txt">Mandatory to attempt all question</span>
                                                </div> 
                                                <!-- <div class="addtest-checkbox">
                                                    <span class="cb-radio checkbox-btn">
                                                    <input < ?php echo $test['a_submit_immediate']==1?'checked="checked"':''; ?> name="test_submit_immediate" id="test_submit_immediate" value="1" type="checkbox" class="rdobtn question-option-input">
                                                        <label class="rdoinr rdc">
                                                            <span class="inrrclr"></span>
                                                        </label>                                        
                                                    </span>   
                                                    <span class="download-txt">Submit immediately after answering</span>
                                                </div>    -->
                                                <input name="test_submit_immediate" id="test_submit_immediate" value="<?php echo $test['a_submit_immediate']; ?>" type="hidden" class="rdobtn question-option-input">
                                            </div>    
                                        </div>
                                    </div>
                                    
                                    
                                    
                                    <div class="row">
                                        <div class="col-sm-12 arrangement-grouping">
                                            <div class="arrangement-panel">End Quiz Settings</div>
                                            <div class="addtest-container endtest">
                                                <div class="addtest-checkbox">
                                                    <span class="cb-radio checkbox-btn">
                                                    <input <?php echo $test['a_show_smessage']==1?'checked="checked"':''; ?> id="test_submit_response" name="test_submit_response" value="1" type="checkbox" class="rdobtn question-option-input">
                                                        <label class="rdoinr rdc">
                                                            <span class="inrrclr"></span>
                                                        </label>                                        
                                                    </span>   
                                                    <span class="download-txt">Message after submitting the quiz.</span>
                                                    
                                                   
                                                    <div class="col-sm-12 generated-area" id="submission_message" <?php echo $test['a_show_smessage']==0?'style="display:none;"':''; ?>>
                                                        Message to be shown* :
                                                        <textarea name="test_submit_message" id="test_submit_message" maxlength="50" class="form-control" placeholder="Test Name"><?php echo $test['a_smessage']; ?></textarea>
                                                    </div>                                                    
                            
                                                </div>
                                    
                                            </div>    
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-12 arrangement-grouping">
                                            <div class="arrangement-panel">Evaluation Settings</div>
                                            <div class="addtest-container endtest">
                                                <div class="addtest-checkbox">
                                                    <span class="cb-radio checkbox-btn">
                                                    <input <?php echo $test['a_has_pass_fail']==1?'checked="checked"':''; ?> id="test_has_passfail" name="test_has_passfail" value="1" type="checkbox" class="rdobtn question-option-input">
                                                        <label class="rdoinr rdc">
                                                            <span class="inrrclr"></span>
                                                        </label>                                        
                                                    </span>   
                                                    <span class="download-txt">Enable Pass/Fail</span>
                                                    
                                                    <span id="pass-fail-block" <?php echo $test['a_has_pass_fail']==0?'style="display:none;"':''; ?>>
                                                        <div class="col-sm-12 generated-area" id="submission_messag">
                                                            Pass Marks (%)* :
                                                            <div class="input-group mtb715">
                                                                <input type="text" onkeypress="return isNumber(event)" name="test_pass_percentage" id="test_pass_percentage" maxlength="4" value="<?php echo $test['a_pass_percentage']; ?>" class="form-control" placeholder="eg: 45" aria-describedby="basic-addon1" data-validation="number">
                                                                <span class="input-group-addon" id="basic-addon1">%</span>
                                                            </div>
                                                        </div>

                                                        <span class="cb-radio checkbox-btn">
                                                            <input <?php echo $test['a_fail_pass_message']==1?'checked="checked"':''; ?> id="test_passfail_response" name="test_passfail_response" value="1" type="checkbox" class="rdobtn question-option-input">
                                                                <label class="rdoinr rdc">
                                                                    <span class="inrrclr"></span>
                                                                </label>                                        
                                                        </span>   
                                                        <span class="download-txt">Pass/Fail Feedback</span>
                                                        
                                                        <div class="col-sm-12 generated-area pass-fail-item" id="pass_message_area" <?php echo $test['a_fail_pass_message']==0?'style="display:none;"':''; ?>>
                                                            Feedback for pass* :
                                                            <textarea name="test_pass_message" id="test_pass_message" maxlength="50" class="form-control" placeholder="Pass Message"><?php echo $test['a_pass_message']; ?></textarea>
                                                        </div>

                                                        <div class="col-sm-12 generated-area" id="fail_message_area" <?php echo $test['a_fail_pass_message']==0?'style="display:none;"':''; ?>>
                                                            Feedback for fail* :
                                                            <textarea name="test_fail_message" id="test_fail_message" maxlength="50" class="form-control" placeholder="Fail Message"><?php echo $test['a_fail_message']; ?></textarea>
                                                        </div>
                                                    </span>
                                                    

                                                </div>
                                    
                                            </div>    
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-12 arrangement-grouping">
                                            <div class="arrangement-panel">Candidate Reports Settings</div>
                                            <div class="addtest-container">
                                                <div class="addtest-checkbox">
                                                    <span class="cb-radio checkbox-btn">
                                                    <input <?php echo ($test['a_que_report']==1||$test['a_test_report']==1)?'checked="checked"':''; ?> name="test_reports" id="test_all_reports" value="1" type="checkbox" class="rdobtn question-option-input">
                                                        <label class="rdoinr rdc">
                                                            <span class="inrrclr"></span>
                                                        </label>                                        
                                                    </span>   
                                                    <span class="download-txt">Show Reports</span>
                                                </div>
                                            
                                                <div class="addtest-checkbox">
                                                    <span class="cb-radio checkbox-btn" id="test_immediate_sub">
                                                    <input <?php echo ($test['a_que_report']==0&&$test['a_test_report']==0)?'disabled="disabled"':''; ?> <?php echo $test['a_que_report']==1?'checked="checked"':''; ?> name="test_que_report" id="test_que_report" value="1" type="checkbox" class="rdobtn question-option-input">
                                                        <label class="rdoinr rdc">
                                                            <span class="inrrclr"></span>
                                                        </label>                                        
                                                    </span>   
                                                    <span class="download-txt">Immediate after the each question submission</span>
                                                </div>

                                                <div class="addtest-checkbox">
                                                    <span class="cb-radio checkbox-btn" id="test_quiz_sub">
                                                    <input <?php echo ($test['a_que_report']==0&&$test['a_test_report']==0)?'disabled="disabled"':''; ?> name="test_end_report" id="test_end_report" <?php echo $test['a_test_report']==1?'checked="checked"':''; ?> value="1" type="checkbox" class="rdobtn question-option-input">
                                                        <label class="rdoinr rdc">
                                                            <span class="inrrclr"></span>
                                                        </label>                                        
                                                    </span>   
                                                    <span class="download-txt">After quiz submission</span>
                                                </div>    
                                            </div>    
                                        </div>
                                    </div>

                                </div>
                                
                                
        
                            </div>
                        
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <input onclick="saveNext()" type="button" id="saveNext_button" class="pull-right btn btn-green marg10" data-div="step-two" value="SAVE & NEXT">
                                    <input onclick="save()" type="button" id="save_button" class="pull-right btn btn-green marg10" data-div="step-two" value="SAVE">
                                </div>
                            </div>
                            <input type="hidden" name="savenext" id="savenextform" value="0">
                            <input type="hidden" name="submitted" id="submittedform" value="0">
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- =========================== -->
        <!-- Nav section inside this wrap  --> <!-- END -->
    </div>
    <!-- ==========================  -->
    <!--  LEFT CONTENT--> <!-- ENDS -->
</section>

<!-- JS -->
<script src="<?php echo assets_url() ?>js/redactor/js/redactor.js"></script>
<script src="<?php echo assets_url() ?>js/redactor/js/table.js"></script>
<script src="<?php echo assets_url() ?>js/redactor/js/source.js"></script>
<script src="<?php echo assets_url() ?>js/redactor/js/alignment.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        //$("#test_immediate_sub").css("visibility","hidden");
        //$("#test_quiz_sub").css("visibility","hidden");
        $('#test_all_reports').change(function(){
            $('#test_que_report').attr('checked', false);
            $("#test_end_report").attr('checked', false);
            if($(this).is(":checked")){
                $("#test_que_report").removeAttr("disabled");
                $("#test_end_report").removeAttr("disabled");
                $("#test_immediate_sub").css("visibility","visible");
                $("#test_quiz_sub").css("visibility","visible");
            }else{
                //$("#test_que_report").removeAttr("checked");
                //$("#test_end_report").removeAttr("checked");
                $("#test_que_report").attr("disabled","disabled");
                $("#test_end_report").attr("disabled","disabled");
                $("#test_immediate_sub").css("visibility","hidden");
                $("#test_quiz_sub").css("visibility","hidden");
            }       
        });

        if($("#test_all_reports").is(":checked")){
            $("#test_que_report").removeAttr("disabled");
            $("#test_end_report").removeAttr("disabled");
            $("#test_immediate_sub").css("visibility","visible");
            $("#test_quiz_sub").css("visibility","visible"); 
        } else {
            $("#test_que_report").attr("disabled","disabled");
            $("#test_end_report").attr("disabled","disabled");
            $("#test_immediate_sub").css("visibility","hidden");
            $("#test_quiz_sub").css("visibility","hidden");
        }

        $('#test_passfail_response').change(function(){
            if($(this).is(":checked")){
                $("#test_pass_message").val('');
                $("#test_fail_message").val('');
                $("#pass_message_area").show();
                $("#fail_message_area").show();
            }else{
                $("#pass_message_area").css("display","none");
                $("#fail_message_area").css("display","none");
            }       
        });

        $('#test_submit_response').change(function(){
            if($(this).is(":checked")){
                $("#test_submit_message").val('');
                $("#submission_message").show();
            }else{
                $("#submission_message").css("display","none");
            }       
        });

        $('#test_has_passfail').change(function(){
            if($(this).is(":checked")){
                $("#test_pass_percentage").val('0');
                $("#pass-fail-block").show();
            }else{
                $('#test_passfail_response').attr('checked', false);
                $("#pass-fail-block").css("display","none");
            }       
        });
    });

  

    function isNumber(evt) {
        evt = (evt) ? evt : window.event;
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if (charCode > 31 && (charCode < 48 || charCode > 57)) {
            return false;
        }
        return true;
    }

    function saveNext(){
        $('#savenextform').val('1');
        $('#submittedform').val('1');
        if(validateStep('step-two')){
            $('#save_test_basics').submit();
        }else{
            return false;
        }

    }

    function save(){
        $('#savenextform').val('0');
        $('#submittedform').val('1');
        if(validateStep('step-two')){
            $('#save_test_basics').submit();
        }else{
            return false;
        }

    }


    function validateStep(step){
        switch(step){
            case 'step-two':
                var msg = '';
                if($('#test_passfail_response').prop('checked')){
                    if($('#test_pass_message').val() == ''){
                        msg = 'Please enter a valid pass response.';
                    }
                    if($('#test_fail_message').val() == ''){
                        msg = 'Please enter a valid fail response.';
                    }
                }
                if($('#test_submit_response').prop('checked')){
                    if($('#test_submit_message').val() == ''){
                        msg = 'Please enter a submission response.';
                    }
                }
                if($('#test_has_passfail').prop('checked')){
                    if($('#test_pass_percentage').val() == '' || Number($('#test_pass_percentage').val()) > 100 || Number($('#test_pass_percentage').val()) <= 0){
                        msg = 'Please enter a valid pass percentage.<br/>';
                    }
                }
                if(msg!=''){
                    var messageObject = {
                        'body': msg,
                        'button_yes':'OK', 
                        'button_no':'CANCEL'
                    };
                    callback_warning_modal(messageObject);
                    return false;
                }
            break;
        }

        return true;
    }
</script>

<?php include_once 'footer.php';?>

<!-- Modal pop up contents:: Delete Section popup-->
    <div class="modal fade alert-modal-new" id="test_basic_modal" role="dialog" 
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
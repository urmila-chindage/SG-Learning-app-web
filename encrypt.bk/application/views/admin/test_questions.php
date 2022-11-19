<?php include_once 'coursebuilder/lecture_header.php';?>
<!-- ADDING REDACTOR PLUGIN INSIDE -->
<!-- ############################# --> <!-- START -->
    <link rel="stylesheet" href="<?php echo assets_url() ?>css/redactor/css/redactor.css" />
    <link rel="stylesheet" href="<?php echo assets_url() ?>css/redactor/css/alignment.css" />

<!-- ############################# --> <!-- END -->
<link rel="stylesheet" href="<?php echo assets_url() ?>css/datepicker.min.css">
<link rel="stylesheet" href="<?php echo assets_url() ?>css/timepicker.css">
<link rel="stylesheet" href="<?php echo assets_url() ?>css/autocomplete.css" />
<link href="<?php echo assets_url() ?>css/bootstrap-tagsinput.css" rel="stylesheet" type="text/css" />
<style type="text/css">
    .mark-box{
        width: 75px;
        margin: 10px;
        text-align: center;
    }
    .question-master-select {
    display: inline-block;
    width: 150px;
}
</style>
<!--Tag input js-->
<script src="<?php echo assets_url() ?>js/bootstrap-tagsinput.js" type="text/javascript"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/datepicker.js"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/datepicker.en.js"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/jquery.timepicker.js"></script>
<!-- MAIN TAB --> <!-- STARTS -->
<?php include_once('test_header.php'); ?>
<div class="right-wrap small-width base-cont-top-heading container-fluid pull-right rightwrap-top-update">
    <br/>
    <div class="list-group">
      <a href="javascript:void(0)" onclick="saveConfirmattion(3)" class="btn btn-green btn-big full-width-btn selected">Import From Question Bank<ripples></ripples></a>
        <a href="javascript:void(0)" class="btn btn-big btn-green full-width-btn" id="generate_upload_question" onclick="saveConfirmattion(4)">
            Upload Question        <ripples></ripples></a>
      <a href="javascript:void(0)" onclick="saveConfirmattion(5)" class="btn btn-green btn-big full-width-btn selected">Add Question<ripples></ripples></a>
    <!--button type="button" class="btn btn-info" data-toggle="modal" data-target="#report-card">Open Modal</button-->
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
                        <!-- <span class="pull-left"><input type="checkbox" id="selectall">Select All</span> -->
                        <?php
                        if(!empty($questions)){
                        ?>

                        <span class="pull-left" style="margin-left:1em;"><a href="#!."><label><input type="checkbox" id="selectall"><span class="select-span" id="sel-all">Select All</span><label class="rdoinr rdc"><span class="inrrclr"></span></label><span id="selected_institutes_count"></span></a></span>
                  
                      
                                <div class="btn-group lecture-control btn-right-align pull-left" id="bulk-action" style="margin-top:0px;margin-left:10px;display:none;">
                                    <span class="dropdown-tigger" data-toggle="dropdown">
                                        <span class='label-text'>
                                           Bulk Action  <!-- <span class="icon icon-down-arrow"></span> -->
                                        </span>
                                        <span class="tilder"></span>
                                    </span>
                                    <ul class="dropdown-menu pull-right" role="menu" style="left: 0;top: 20px;border-top-left-radius: 0px;border-top-right-radius: 3px;">
                                        <li>
                                            <a onclick="bulkDelete();" href="javascript:void(0)">Delete Selected</a> 
                                        </li>
                                        
                                    </ul>
                                </div>                    

                        <div class="test-applynegativebulk">
                            <a href="javascript:void(0)" id="showNegativeBulk">Negative<span class="caret"></span></a>
                        </div>
                        <span id="showNegativeBulkContent" class="shownegativecontents" style="display:none;">
                            <input type="text" onkeypress="return preventAlphabets(event)" class="form-control" value="0" id="bulknegmark">
                            <input type="button" class="btn btn-success" value="Apply" onclick="bulkApplynMark();">                
                        </span>  
                        
                        
                        <div class="test-applybulk">
                            <a href="javascript:void(0)" id="showBulk">Marks<span class="caret"></span></a>
                        </div>
                        <span id="showBulkContent" class="showcontents" style="display:none;">
                            <input type="text" onkeypress="return preventAlphabets(event)" class="form-control" value="1" id="bulkposmark">
                            <input type="button" class="btn btn-success" value="Apply" onclick="bulkApplypMark();">                
                        </span>

                        <!--<input type="button" value="Delete Bulk" onclick="bulkDelete();" class="btn btn-danger">-->
                        <form class="form-horizontal" id="save_test_basics" method="post" action="<?php echo admin_url('test_manager/test_questions'.'/'.base64_encode($test['id'])); ?>">
                            
                            <div class="each-steps step-three" id="step-three">
                                
                            </div>
                            <div class="form-group">
                                <div class="col-sm-12 action10">
                                    <input onclick="saveNext()" type="button" id="saveNext_button" class="pull-right btn btn-green" data-div="step-two" value="SAVE & NEXT">
                                    <input onclick="save()" type="button" id="save_button" class="pull-right btn btn-green marg10" data-div="step-two" value="SAVE">
                                </div>
                            </div>
                        
                            <input type="hidden" name="savenext" id="savenextform" value="0">
                            <input type="hidden" name="submitted" id="submittedform" value="0">
                        <?php } else { 
                            ?>
                        <div class="buldr-header" style="text-align:center;margin-top:1em;">
                        <h3 >No questions found.</h3>
                        </div>
                        <?php } ?>
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
    var asset_url           ='<?php echo assets_url() ?>';
    var __totalMarks        = <?php echo $test['a_mark']; ?>;
    var __quizTotalQuestions= <?php echo $test['a_questions']; ?>;
    var __quizTotalMarks    = <?php echo ($quiz_total_mark!='')?$quiz_total_mark:'0'; ?>;
    var __importQuestionIds = {};
    var __questionsToDelete = [];
    var __questions         = atob('<?php echo base64_encode(json_encode($questions)); ?>');
    var __assessment_id     = '<?php echo base64_encode($test['assessment_id']); ?>';
    var __test_id           = '<?php echo base64_encode($test['id']); ?>';
    var __selectedQuestions = [];
    var __questionIdChecked = 0;
    var __upload_progress      = false;
    $(document).ready(function(){
        __questions         = $.parseJSON(__questions);
        $('#step-three').html(renderQuestions(__questions));
        $("#showBulk").click(function(){
            $("#showBulkContent").slideToggle();
            $("#showNegativeBulkContent").slideUp();
        });
        $("#showNegativeBulk").click(function(){
            $("#showNegativeBulkContent").slideToggle();
            $("#showBulkContent").slideUp();
        });        
    });

    $('#selectall').change(function(){
        if($(this).is(":checked")){
            $('.ques-check').each(function() {
                $(this).prop('checked', true);
                __questionIdChecked = $(this).val();
                __importQuestionIds[__questionIdChecked] = __questionIdChecked;
                $("#bulk-action").show();
            });
        }else{
            $('.ques-check').each(function(){
                $(this).prop('checked', false);
                __importQuestionIds = {};
                $("#bulk-action").hide();
            });
        }
        if(Object.keys(__importQuestionIds).length > 0 )
        {
            var questionCountHtml = Object.keys(__importQuestionIds).length;
            $('#sel-all').html('Select all ('+questionCountHtml+')');
        }
        else
        {
            $('#sel-all').html('Select all');        
        }        
    });

    function isNumber(evt) {
        evt = (evt) ? evt : window.event;
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if (charCode > 31 && (charCode < 48 || charCode > 57)) {
            return false;
        }
        return true;
    }

    function saveConfirmattion(type){
        switch(type){
            case 3:
                window.location = webConfigs('admin_url')+'generate_test/import_questions/'+atob(__test_id);
            break;
            case 4:
               $("#upload_assessment_file").html('');
               uploadQuestionModal();
            break;
            case 5:
                window.location = webConfigs('admin_url')+'generate_test/question/0/'+atob(__assessment_id);
            break;
        }
    }

    function saveNext(){
        $('#savenextform').val('1');
        $('#submittedform').val('1');
        if(validateEachmark()==true){
            if(validateStep()){
                 $('#save_test_basics').submit();
            }
        }
        else
        {
            result = false;
        }
    }

    function save(){
        $('#savenextform').val('0');
        $('#submittedform').val('1');
        if(validateEachmark()==true){
            if(validateStep()){
                 $('#save_test_basics').submit();
            }
        }
        else
        {
            return false;
        }
    }

    function submitConfirmed(){
       $('#save_test_basics').submit();
    }

    function submitCancelled(){
        location.reload();
    }

    
    function renderQuestions(questions){
        var html        = '';
        $.each(questions,function(q_key,question){
            html        += renderQuestion(question,q_key);
        });

        return html;
    }

    
    

    function renderQuestion(question,key){
        var html        = '';
        var quesString  = $.parseJSON(question['q_question']);
        quesString      = quesString[1];
        var regex = /(<([^>]+)>)/ig
        quesString      = quesString.replace(regex,"");
        if(quesString.length > 58){
            quesString      = quesString.substr(0,56)+'...';
        }
        html            += '<div class="default-view-txt test-folder addtest-checkbox test-qstn" id="questionDiv'+question['id']+'">';
        html            += '<span class="cb-radio checkbox-btn"><input type="checkbox" class="import-questions ques-check rdobtn question-option-input" value="'+question['id']+'"><label class="rdoinr rdc"><span class="inrrclr"></span></label></span>';
        html            += '<span class="question-sl-no">'+(key+1)+' .</span>';
        html            += '<span class="question-text">'+jQuery.trim(quesString)+'</span>';
        html            += '<span class="question-type">'+renderQtype(question['q_type'])+'</span>';
        html            += '<span class="question-shortcuts"><input type="text" id="positive'+question['id']+'" name="positive'+question['id']+'" value="'+question['aq_positive_mark']+'"  onkeypress="return isNumber(event)" class="mark-box pos-mark"><input type="text" id="negative'+question['id']+'" name="negative'+question['id']+'" value="'+Math.abs(question['aq_negative_mark'])+'"  onkeypress="return isNumber(event)" class="mark-box">';
        html            += '<a href="'+webConfigs('admin_url')+'generate_test/question/'+question['question_id']+'/'+atob(__assessment_id)+'" title="" class="test-folder-delte" data-toggle="tooltip" data-placement="top" data-original-title="Edit"><i class="icon icon-pencil ic20"></i></a>';
        html            += '<a href="javascript:void(0)" title="" onclick="deleteQuestion(\''+btoa(question['id'])+'\');" data-toggle="tooltip" class="test-folder-delte" data-placement="top" data-original-title="Delete"><i class="icon icon-trash-empty ic20"></i></a></span>';
        html            += '</div>';
        html            += '<input type="hidden" name="question[]" value="'+question['id']+'"></div>';

        return html;
    }

    function renderQtype(type){
        var rreturn = '';
        type = parseInt(type);
        switch(type){
            case 1:
                rreturn = 'Single Choice';
            break;
            case 2:
                rreturn = 'Multiple Choice';
            break
            case 3:
                rreturn = 'Subjective Type';
            break;
            case 4:
                rreturn = 'Fill in the blanks';
            break;
        }

        return rreturn;
    }

    function deletePreviewQuestion(question_id){
        question_id = typeof question_id != 'undefined' ? question_id : '';
        $('#test_pop_title').html('Delete Question');
        $('#test_pop_desc').html('Are you sure to delete this question?<br/>');
        $('#test_pop_continue').attr('onclick','deletePreviewQuestionConfirmed(\''+question_id+'\')');
        $('#test_basic_modal').modal('show');
    }

    

    function deleteQuestion(question_id){
        question_id = typeof question_id != 'undefined' ? question_id : '';
        var messageObject = {
                'body':'Total mark in step 3 will be updated as final total mark, as it differs from step 1.Would you like to delete this question?',
                'button_yes':'CONTINUE', 
                'button_no':'CANCEL',
                'continue_params':{'question_id':question_id}
        };
        callback_warning_modal(messageObject, deleteQuestionConfirmed);
    }

    function deleteQuestionConfirmed(params){
        __importQuestionIds = {};
        __questionsToDelete = [];
        __questionsToDelete.push(atob(params.data.question_id));
        deleteQuestions(__questionsToDelete);
    }

    function deletePreviewQuestionConfirmed(question_id){
        question_id = typeof question_id != 'undefined' ? question_id : '';
        __questionsToDelete = [];
        __questionsToDelete.push(atob(question_id));
        deletePreviewQuestions(__questionsToDelete);
    }

    function deletePreviewQuestions(questions){
        $('#test_pop_continue').html('Deleting...');
        $.ajax({
            url: webConfigs('admin_url')+'test_manager/delete_preview_questions',
            type: "POST",
            data:{ "is_ajax":true,"assessment_id":atob(__assessment_id),"questions":JSON.stringify(questions),'lecture_id':__test_id},
            success: function(response){
                $('#test_pop_cancel').click();
                var reviewHtml        = '';
                $('#test_pop_continue').html('Continue');
                var question          = JSON.parse(response);
                $('#question_preview_count').html(Object.keys(question['question']).length);
                reviewHtml            += renderPreviewQuestions(question['question']);
                $('#review-question').html(reviewHtml);
                $('#report-card').modal('show');
           
            }
        });
    }

    function savePreviewQuestions(){
        $('#test_preview_continue').html('Saving...');
        $.ajax({
            url: webConfigs('admin_url')+'generate_test/confirm_import_questions',
            type: "POST",
            data:{ "is_ajax":true,"assessment_id":atob(__assessment_id),'lecture_id':__test_id},
            success: function(response){
                var data  = $.parseJSON(response);
                $('#test_preview_continue').html('save');
                if(data['success'] == true){
                   setTimeout(function(){window.location.reload();}, 500)
                }
            }
        });
    }

    function clearPreviewQuestions(){
        $("#q_parent_cat").val('');
        $("#upload_file_name").val('');
        $("#percentage_bar").html('');
        $(".close").trigger("click");
    };
    


    function deleteQuestions(questions){
        $('#test_pop_continue').html('Deleting...');
        $.ajax({
            url: webConfigs('admin_url')+'test_manager/delete_questions',
            type: "POST",
            data:{ "is_ajax":true,"assessment_id":atob(__assessment_id),"questions":JSON.stringify(questions),'lecture_id':__test_id},
            success: function(response){
                //$("#common_message_advanced").hide();
                var data  = $.parseJSON(response);
                if(data['success'] == true){
                    $('#step-three').html(renderQuestions(data['questions']));
                    $("#bulk-action").hide();
                    $('#selectall').attr('checked', false);
                    $('#sel-all').html('Select all');
                    __importQuestionIds = {};
                    if(Object.keys(data['questions']).length == 0){
                        $("#selectall").hide();
                        location.reload();
                    }
                    $(".close").trigger('click');
                    validateMark();
                }else{
                    alert(data['message']);
                }
                
            }
        });
    }

    function validateEachmark(){
        var output = true;
        $(".pos-mark").each(function(){
            if($(this).val()==0){
                var messageObjects = {
                'body': 'Quiz question mark should not be as zero.',
                'button_yes':'CONTINUE', 
                'button_no':'CANCEL'
                };
                callback_warning_modal(messageObjects,submitCancelled);
                output = false;
            } 
        });
        return output;
    }
    function validateStep(){
        var err_cnt        = 0;
        var renderError    = '';
        var totMark        = Number(0);
        $(".pos-mark").each(function(){
            if($(this).val()!=0){
                totMark += parseInt($(this).val());
            } 
        });
        var result = true;
        if(totMark!=__totalMarks){
            var messageObject = {
                'body': 'Total mark in step 3 will be updated as final total mark, as it differs from step 1.Would you like to continue?',
                'button_yes':'CONTINUE', 
                'button_no':'CANCEL'
            };
            callback_warning_modal(messageObject,submitConfirmed);
            result = false;
        } 
        if(__totalMarks!=__quizTotalMarks){
            var messageObject = {
                'body': 'Total mark in step 3 will be updated as final total mark, as it differs from step 1.Would you like to continue?',
                'button_yes':'CONTINUE', 
                'button_no':'CANCEL'
            };
            callback_warning_modal(messageObject,submitConfirmed);
            result = false;
        } 
        return result;

    }

    function validateMark(){
        $.ajax({
            url: webConfigs('admin_url')+'test_manager/validate_mark',
            type: "POST",
            data:{ "is_ajax":true,"assessment_id":atob(__assessment_id),'lecture_id':__test_id},
            success: function(response){
                //$("#common_message_advanced").hide();
                var data  = $.parseJSON(response);
                if(data['success'] == true){
                    __totalMarks = data['total_mark'];
                }
                
            }
        });
    }

    function sum(input){
             
        if (toString.call(input) !== "[object Array]")
            return false;
            var total =  0;
            for(var i=0;i<input.length;i++)
              {                  
                if(isNaN(input[i])){
                continue;
                 }
                  total += Number(input[i]);
               }
             return total;
    }

    function bulkDelete(){
        var messageObject = {
            'body':'Total mark in step 3 will be updated as final total mark, as it differs from step 1.Would you like to delete this questions?',
            'button_yes':'CONTINUE', 
            'button_no':'CANCEL'
        };
        callback_warning_modal(messageObject, bulkDeleteConfirmed);
    }

    function bulkDeleteConfirmed(){
        __selectedQuestions      = [];
        $('.ques-check:checkbox:checked').each(function() {
            __selectedQuestions.push($(this).val());
        });
        deleteQuestions(__selectedQuestions);
    }

    function bulkApplypMark(){
        var questionId          = 0;
        var posMark             = $('#bulkposmark').val();
        $('.ques-check:checkbox').each(function(){
            questionId      = $(this).val();
            $('#positive'+questionId).val(posMark);
            $(this).prop('checked',false);
        });
        $('#bulkposmark').val('');
        $('#showBulkContent').css('display','none');
    }
    function bulkApplynMark(){
        var questionId          = 0;
        var negMark             = $('#bulknegmark').val();
        $('.ques-check:checkbox').each(function(){
            questionId      = $(this).val();
            $('#negative'+questionId).val(negMark);
            $(this).prop('checked',false);
        });
        $('#bulknegmark').val('');
        $('#showNegativeBulkContent').css('display','none');
    }
    
    
    var __uploaded_files       = '';
    function uploadQuestionModal(){
        $("#upload_question").val('');
        __uploaded_files = '';
        $('#test_pop_cancel').click();
        $('#addquestion').modal('show');

    }
    
    $(document).on('change', '#upload_question', function(e){
        //console.log(e.currentTarget.files[0]);
        $('#percentage_bar').hide();
        var i                           = 0;
        __uploaded_files                = e.currentTarget.files[i];
        __uploaded_files['extension']   = __uploaded_files['name'].split('.').pop();
        $('#upload_file_name').val(__uploaded_files['name']);
        //console.log(__uploaded_files);
    });

    function uploadQuestion()
    {
        $('#popUpMessage').remove();
        var category_id = $("#q_parent_cat").val();
        if(category_id==0)
        {
            var messageObject = {
                'body':'Category Cannot be empty!',
                'button_yes':'OK'
            };
            callback_warning_modal(messageObject);
            return false;
        }
        if(__uploaded_files=='')
        {
            //lauch_generate_message('File Missing', 'Choose any file to upload');
            var messageObject = {
                'body':'File Missing!</br>Choose any file to upload',
                'button_yes':'OK'
            };
            callback_warning_modal(messageObject);
            return false;
        }

        var filename = __uploaded_files['name'];
        //var valid_extensions = /(\.xls)$/i;   
        var valid_extensions = /(\.xls|\.docx|\.doc)$/i;  

        if(valid_extensions.test(filename))
        {
            $('#percentage_bar').show();
            $("#save_upload_question").html('Uploading<img src="'+asset_url+'images/loader.svg" width="25">');
            var uploadURL               = admin_url+'generate_test/upload_question'
            var param                   = new Array;
            param["file"]               = __uploaded_files;
            param["category_id"]        = category_id;
            param['assessment_id']      = atob(__assessment_id);
            param['processing']         = 'question_upload_processing';
            if(__upload_progress == false){
                __upload_progress   = true;
                uploadFiles(uploadURL, param, uploadQuestionCompleted);
            }else{
                lauch_generate_message('Wait', 'Please wait until upload completes');     
            }    
        }
        else
        {
           lauch_generate_message('Invalid File', 'Choose proper file to upload');
           return false;
        }

    }

    function lauch_generate_message(header, message)
    {
        $('#generate_message_header').html(header);
        $('#generate_message_content').html(message);
        $('#common_message_button').addClass('btn-red').removeClass('btn-green');
        $('#common_message_generate').modal('show');
    }
    
    function uploadQuestionCompleted(response)
    {
        var data = $.parseJSON(response);
        __uploaded_files = '';
        __upload_progress   = false;
        __uploading_file = '';
        if(data['success'] == true)
        {
            $("#addquestion .modal-body").prepend(renderPopUpMessage('success', 'Review the uploaded questions'));
            
            if(typeof data['html_file'] != 'undefined') {
                $('#percentage_bar span').html('Parsing the template...');
                var xhr= new XMLHttpRequest();
                xhr.open('GET', data['html_file'], true);
                xhr.onreadystatechange= function() {
                    if (this.readyState!==4) return;
                    if (this.status!==200) return; // or whatever error handling you want
                    document.getElementById('question_template_preview').innerHTML= this.responseText;
                    var dom = new DocumentReader('#question_template_preview > table', data['uploaded_object']);
                    var category_id = $('#q_parent_cat').val();
                    var documentParsed = dom.parseDocument();
                    if(documentParsed['error'] == false) {
                        $.ajax({
                                url: admin_url+"generate_test/load_parsed_document",
                                type: "POST",
                                data:{ "lecture_id":atob(__test_id), 'category_id':category_id, 'upload_data':data['uploaded_object'], 'doc_details':dom.getDocumentProperties(), 'doc_objects':JSON.stringify(documentParsed['question'])},
                                success: function (response){
                                    var data = $.parseJSON(response);
                                    if(data['success'] == true) {
                                        $("#addquestion .modal-body").prepend(renderPopUpMessage('success', 'Review the uploaded questions'));
                                        window.location = admin_url+'generate_test/upload_question_preview';    
                                    } else {
                                        $("#upload_question").val('');
                                        $("#upload_file_name").val('');
                                        __uploaded_files = '';
                                        $('#percentage_bar').hide();
                                        $("#save_upload_question").html('UPLOAD');
                                        $('#question_upload_processing_wrapper').html('Uploading...<b class="percentage-text" id="question_upload_processing">0%</b>');
                                        $("#addquestion .modal-body").prepend(renderPopUpMessage('error', '<div>Error occured in upload question. Fix the following issue and re-upload the template again. <br />'+data['message']+'</div>'));        
                                        scrollToTopOfPage();                        
                                    }
                                },
                        });
                    } else {
                        $("#upload_question").val('');
                        $("#upload_file_name").val('');
                        __uploaded_files = '';
                        $('#percentage_bar').hide();
                        $("#save_upload_question").html('UPLOAD');
                        $('#question_upload_processing_wrapper').html('Uploading...<b class="percentage-text" id="question_upload_processing">0%</b>');
                        $("#addquestion .modal-body").prepend(renderPopUpMessage('error', '<div>Error occured in upload question. Fix the following issue and re-upload the template again. <br />'+documentParsed['message']+'</div>'));        
                        scrollToTopOfPage();                        
                    }

                };
                xhr.send();
            } else {
                var uploadData  = $.parseJSON(response);
                if(uploadData['success'] == true){
                    window.location = admin_url+'generate_test/upload_question_preview';
                }
            }
        
        }
        else
        {
            $("#upload_question").val('');
            $("#upload_file_name").val('');
            __uploaded_files = '';
            $('#percentage_bar').hide();
            $("#save_upload_question").html('UPLOAD');
            $('#question_upload_processing_wrapper').html('Uploading...<b class="percentage-text" id="question_upload_processing">0%</b>');
            $("#addquestion .modal-body").prepend(renderPopUpMessage('error', '<div>Error occured in upload question. Fix the following issue and re-upload the template again. <br />'+data['message']+'</div>'));        
            scrollToTopOfPage();
        }
    }
    function toAlpha(number)
    {
    var alphabet = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'];
    //number--;
    var count = alphabet.length;
    if(number <= count)
        return alphabet[number-1];
    var modulo = 0, alpha = '';
    while(number > 0){
        modulo     = (number - 1) % count;
        alpha      = alphabet[modulo]+alpha;
        number     = Math.floor(((number - modulo) / count));
    }
    return alpha;
    }

$(document).on('change', '.import-questions', function(){
    //alert(Object.keys(__questions).length);
    $('#selectall').attr('checked', false); 
    __questionIdChecked = $(this).val();
    if($(this).prop('checked') == true)
    {
        __importQuestionIds[__questionIdChecked] = __questionIdChecked;
    }
    else
    {
        delete __importQuestionIds[__questionIdChecked];
    }
    var questionCountHtml = Object.keys(__importQuestionIds).length;
    if(Object.keys(__importQuestionIds).length > 0 )
    {
       // alert('dfdf1');
        $("#bulk-action").show();
        $('#sel-all').html('Select all ('+questionCountHtml+')');
        if((Object.keys(__importQuestionIds).length)==__quizTotalQuestions){
           $('#selectall').trigger('click');
        }
    }
    else
    {
        $("#bulk-action").hide();   
        $('#sel-all').html('Select all'); 
        $('#selectall').attr('checked', false);      
    }
    var all_list = $('.import-questions:visible').length;
    var checked_list = $('.import-questions:checked').length;
    if(all_list == checked_list){

        $('#selectall').prop('checked', true);
    }else{
        $('#selectall').prop('checked', false);
    }
});
function isNumber(evt) 
{
    evt = (evt) ? evt : window.event;
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    // if (charCode > 31 && (charCode < 48 || charCode > 57)) {
    //     return false;
    // }
    if ((evt.which != 46 || $(this).val().indexOf('.') != -1) && (evt.which < 48 || evt.which > 57)) {
        evt.preventDefault();
        return false;
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
<div id="question_template_preview" style="display:none;">
</div>
     


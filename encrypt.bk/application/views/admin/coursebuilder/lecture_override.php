<link rel="stylesheet" href="<?php echo assets_url() ?>css/jquery.timepicker.min.css">
<link rel="stylesheet" href="<?php echo assets_url() ?>css/jquery-ui.css">
<style>
        	.custom-width{
    		max-width:780px; 
    	}	
    	.overrided-info p{
    		font-size: 14px;
            font-weight: normal!important;
		    padding: 30px 0 20px 0;
		    color: #444;
    	}
    	.hr-line{
    		background: #d1d1d1;
    		height: 1px;
    	}
    	.overrided-rules{
    		border: 1px solid #ccc;
		    border-radius: 6px;
		    text-align: left;
		    padding: 20px;
            margin-bottom : 1em;
    	}
    	.overrided-rules label{
    		font-size: 14px;
    		font-weight: 600;
    	}
    	.overrided-rules .green-text{
    		color: #33b565;
    	}
    	.overrided-rules .red-text{
    		color: #ff3333;
    	}
    	.margin-15{
    		margin-bottom: 15px;
    	}
        .modal-btn{
            padding: 20px;
            padding-left: 0;
        }
        .fixed-head {
            position: sticky;
            top: -50px;
            background: #fff;
            z-index: 9999;
        }
        .filter_dropdown_text{
            color: #444;
            font-weight: 600;
        }
         .input-padding{
            padding-top: 15px;
         }
         .ui-timepicker-container{
            z-index: 9999 !important;
         }  
          .custom-select.select-group{
            height: 100%;
            border: none;
            background: #eeeeee;
         }
        select:hover,select:active,select:focus 
        {
            outline:none !important;
        }
        .overided-chips{
            border: 1px solid #470871;
            border-radius: 16px;
            padding: 0px 10px;
            line-height: 25px;
            margin-right: 10px;
            display: inline-block;
        }
</style>

   <!-- accordion starts here -->
    <div class="panel-group" id="accordion">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="panel-title">
                    <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false">
                        <h4 class="coursebuilder-settingstab-title">Batch Override Settings</h4>
                    </a>
                </div>
            </div>
            <div id="collapseTwo" class="panel-collapse collapse">
                <div class="panel-body">
                    <!-- override starts here -->
                    <div class="builder-inner-from" id="descriptive_wrapper_form">
                        <div class="row" id="override_info">
                                    
                            <?php
                            if(!empty($override_details)){
                            foreach($override_details as $override_detail){
                                ?>
                                <div class="overrided-rules" id="override_rule_<?php echo $override_detail['id']; ?>">
                                    <div class="overrided-info1">
                                    <p><?php 
                                    $override_groups = explode(",",$override_detail['groups']);
                                    foreach($override_groups as $override_group) {
                                        echo '<span class="overided-chips">'.$override_group.'</span>';
                                    } ?></p>
                                    </div>
                                    <div class="row">   
                                        <div class="col-md-6">
                                            <label>Last Date  : <span class="green-text"><?php if($override_detail['end_date']!=NULL){ echo date("d-m-Y", strtotime($override_detail['end_date'])).' '.$override_detail['end_time']; } ?></span></label>
                                        </div>
                                        <!-- <div class="col-md-6">
                                            <label>Attempts  : <?php //if($override_detail['attempts']==0){ echo 'Unlimited'; } else { echo $override_detail['attempts']; }; ?></label>
                                        </div> -->
                                        <div class="col-md-6 text-right">
                                            <label>Grace Period  : <?php echo $override_detail['period']; ?> 
                                            <?php if($override_detail['period_type']==1){ echo 'Days'; }
                                                else if($override_detail['period_type']==2){ echo 'Hours'; } 
                                                else if($override_detail['period_type']==3){ echo 'Mintutes'; } 
                                            ?> </label>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div style="padding-top: 20px;">
                                            <button type="button" class="btn btn-success pull-right" data-toggle="modal" data-target="#over-insit" onclick="editOverride(<?php echo $override_detail['id']; ?>)">Edit</button>
                                            <button type="button" class="btn btn-danger pull-right"  onclick="deleteOverride(<?php echo $override_detail['id']; ?>)">Delete</button>
                                        </div>
                                    </div>
                                    
                                </div>
                            <?php
                                }
                            } else {?>
                                <div class="overrided-rules" id="no-override">
                                    <div class="row" style="text-align:center;"><p >No override yet.</p></div>
                                </div>
                            <?php }?>

                                  
                                </div>
                                  <!-- modal starts -->
                                  <div class="modal-btn">
                                        <button type="button" class="btn btn-success" data-toggle="modal" data-target="#over-insit" id="add_override">Override by Batch</button>
                                    </div>
                                    <!-- modals -->
                            </div>
                            <!-- override ends here -->
                        </div>
                    </div>

                        <!-- override modals -->
   <!-- override modals -->
   <div class="modal fade in" id="over-insit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-small" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" class="icon icon-cancel-1"></span>
                    </button>
                    <h4 class="modal-title" id="create_box_title">BATCH OVERRIDE SETTINGS</h4>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger" id="an_error" style="display:none;"></div>
                    <div class="ann_add_step1" style="display: none;">
                        <div class="form-group">
                            <label for="">Title: * </label>
                            <br>
                            <input type="text" name="an_title" id="an_title" onkeyup="validateMaxLength(this.id)" maxlength="50" class="form-control" placeholder="Announcement Title">
                            <span id="an_title_char_left" class="pull-right light-grey">45 Characters left</span>
                        </div>
                        <div class="form-group">
                            <label for="">Description:</label>
                            <div class="redactor-box" role="application" dir="ltr">
                                <ul class="redactor-toolbar" id="redactor-toolbar-0" role="toolbar">
                                    <li><a href="javascript:void(null);" class="re-button re-format redactor-toolbar-link-dropdown" title="Format" rel="format" role="button" aria-label="Format" tabindex="-1" aria-haspopup="true">Format</a></li>
                                    <li><a href="javascript:void(null);" class="re-button re-bold" title="Bold" rel="bold" role="button" aria-label="Bold" tabindex="-1">B</a></li>
                                    <li><a href="javascript:void(null);" class="re-button re-italic" title="Italic" rel="italic" role="button" aria-label="Italic" tabindex="-1">I</a></li>
                                      <li><a href="javascript:void(null);" class="re-button re-deleted" title="Strikethrough" rel="deleted" role="button" aria-label="Strikethrough" tabindex="-1">S</a></li>
                                    <li><a href="javascript:void(null);" class="re-button re-lists redactor-toolbar-link-dropdown" title="Lists" rel="lists" role="button" aria-label="Lists" tabindex="-1" aria-haspopup="true">Lists</a></li>
                                    <li><a href="javascript:void(null);" class="re-button re-image" title="Image" rel="image" role="button" aria-label="Image" tabindex="-1">Image</a></li>
                                    <li><a href="javascript:void(null);" class="re-button re-table redactor-toolbar-link-dropdown" title="Table" rel="table" role="button" aria-label="Table" tabindex="-1" aria-haspopup="true">Table</a></li>
                                    <li><a href="javascript:void(null);" class="re-button re-link redactor-toolbar-link-dropdown" title="Link" rel="link" role="button" aria-label="Link" tabindex="-1" aria-haspopup="true">Link</a></li>
                                    <li><a href="javascript:void(null);" class="re-button re-horizontalrule" title="Line" rel="horizontalrule" role="button" aria-label="Line" tabindex="-1">Line</a></li>
                                    <li><a href="javascript:void(null);" class="re-button re-alignment redactor-toolbar-link-dropdown" title="Align" rel="alignment" role="button" aria-label="Align" tabindex="-1" aria-haspopup="true">Align</a></li>
                                </ul><span class="redactor-voice-label" id="redactor-voice-0" aria-hidden="false">Rich text editor</span>
                                <div class="redactor-editor redactor-in redactor-relative" aria-labelledby="redactor-voice-0" role="presentation" id="redactor-uuid-0" contenteditable="true" dir="ltr" style="min-height: 250px; max-height: 250px;" placeholder="Enter Announcement">
                                    <p>&#8203;dfgdfg</p>
                                </div>
                                <textarea name="an_description" id="an_description" placeholder="Enter Announcement" onkeyup="validateMaxLength(this.id)" maxlength="1000" class="form-control redactor" rows="2" style="display: none;"></textarea>
                            </div>
                            <span id="an_description_char_left" class="pull-right light-grey">maximum characters1000 </span>
                        </div>
                    </div>
                    <div class="ann_add_step2" style="">
                        <div class="clearfix"></div>
                        <div class="institution-select" style="display: block;">
                            <div class="inside-box pos-rel pad-top50" id="users_new_group_wrapper" style="overflow-x: hidden;">
                                <div id="render_data" class="container-fluid nav-content pos-abslt width-100p nav-js-height">
                                    <div class="row">
                                        <div class="rTable content-nav-tbl normal-tbl fixed-head">
                                            <div class="rTableRow">
                                                <div class="rTableCell">
                                                    <a href="javascript:void(0)" class="select-all-style">
                                                        <label><input onclick="selectAllGroup('groupAll', 'group-course')" value="1" class="select-users-new-group-parent" id="groupAll" type="checkbox"> Select All (<span id="count_reflect">0</span>/<?php echo count($course_groups); ?>)</label>
                                                    </a>
                                                </div>
                                                <div class="rTableCell dropdown" style="min-width:300px;">
                                       
                                                    <a href="javascript:void(0)" class="dropdown-toggle min-width115" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" id="filter_dropdown_text">Select Institution<span class="caret"></span></a>
                                                    <ul class="dropdown-menu white override-dropdown">
                                                        <li><a href="javascript:void(0)"  onclick="filter_ins_by('all')" id="gfilter_all" >All</a></li>
                                                        <?php
                                                        foreach($institution as $institute):
                                                         ?>
                                                        <li><a href="javascript:void(0)" id="gfilter_<?php echo $institute['id']; ?>"  onclick="filter_ins_by('<?php echo $institute['id']; ?>')"><?php echo $institute['ib_name']; ?></a></li>
                                                        <?php
                                                        endforeach;
                                                        ?>
                                                        
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="add-selectn alignment-order">
                                            <div class="inside-box-padding" id="batch-input">
                                                <?php
                                                foreach($course_groups as $course_group){
                                                    ?>
                                                     <div class="checkbox-wrap group-filter" id="group_course_<?php echo $course_group['id']; ?>" data-gpfilter="<?php echo $course_group['gp_institute_id']; ?>">
                                                    <span class="chk-box">
                                                        <label class="font14">
                                                            <input type="checkbox" class="group-course" id="gr_course_<?php echo $course_group['id']; ?>" value="<?php echo $course_group['id']; ?>"><?php echo $course_group['gp_institute_code'].'-'.$course_group['gp_year'].'-'.$course_group['gp_name']; ?>
                                                        </label>
                                                    </span>
                                                    <span class="email-label pull-right"></span>
                                                    </div>
                                                    <?php
                                                }
                                                ?>
                                               
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <!-- <a href="Batch-overrides.html"> -->
                        <button class="btn btn-green pull-right add-continue" data-step="2" data-action="add" data-canid="" data-toggle="modal" id="batch-modal" >Continue</button>
                    <!-- </a> -->
                    <!-- <a href=""> -->
                        <button type="button" id="step1_close" class="btn btn-red" data-dismiss="modal">CANCEL</button>
                    <!-- </a> -->
                </div>
            </div>
        </div>
    </div>
    <!-- override modals -->

    <!-- batch modal starts -->
    <div class="modal fade in" id="batchModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-small" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" class="icon icon-cancel-1"></span>
                    </button>
                    <h4 class="modal-title" id="create_box_title">Batch Override Settings</h4>
                </div>
                <div class="modal-body">
                   
                    <!-- input row 3 -->
                    <div class="row input-padding">
                        <div class="col-md-6">
                            <label>Last Date*</label>
                            <input type="text" class="form-control" name="end-date"  id="end-date" value="<?php echo isset($test_details['dt_last_date'])?date("d-m-Y", strtotime($test_details['dt_last_date'])):''; ?>" placeholder="dd-mm-yyyy" style="background:#fff;" readonly="" />
                            <!--input type="text" placeholder="Enter the start date" class="form-control hasDatepicker" id="start-date" autocomplete="off"-->
                        </div>
                        <div class="col-md-6">
                            <label>Grace Period</label>
                                <div class="input-group">
                                    <input type="number"  oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="3" min="0" class="form-control cb_validity" name="cb_validity" type="number" min="0" onkeypress="return isNumber(event)" id="grace-period" placeholder="Eg: 180" aria-describedby="basic-addon1">
                                    <span class="input-group-addon no-padding" id="basic-addon1">
                                        <select class="custom-select select-group" id="grace-period-type"  style="" >
                                            <option value="1">Days</option>
                                            <option value="2">Hours</option>
                                            <option value="3">Minutes</option>
                                        </select>
                                    </span>
                                </div>
                        </div>
                        <!-- <div class="col-md-6">
                            <label>Number of Attempts (0 for unlimited)</label> -->
                            <input type="hidden" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="3" min="0" id="attempts"  class="form-control" onkeypress="return isNumber(event)" placeholder="Eg : 1,5,10.." <?php if(isset($test['a_total_attempt'])){ ?>value="<?php echo $test['a_total_attempt']; ?>" <?php } else {?>value="0"<?php } ?> autocomplete="off"  data-validation="number">
                        <!-- </div> -->
                    </div> 
                    <!-- grace marks -->
                    <!-- <div class="row input-padding">
                        
                        <div class="col-md-6"></div>
                    </div> -->
                    <!-- row ends -->
                </div>
                <div class="modal-footer">
                    <button type="button" id="step2_close" class="btn btn-red" data-dismiss="modal">CANCEL</button>
                    <button class="btn btn-green pull-right add-continue" data-step="1" id="save-override">SAVE</button>
                </div>
            </div>
        </div>
    </div>
    <!-- batch modal ends -->




                </div>
            </div>
<!-- accordion ends here -->
<script type="text/javascript" src="<?php echo assets_url() ?>js/jquery-ui.min.js"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/jquery.timepicker.min.js"></script>
    <script>
      var __assign_option   = 0;
      var __lecture_id   = '<?php echo $test_details['dt_lecture_id'] ?>';
      var __inst_selected   = new Array();
      var __group_selected  = new Array();
      var __sel_institute   = '';
      $('#attempts,#grace-period').bind("cut copy paste",function(e) {
     e.preventDefault();
      });
      function filter_ins_by(filter) {
            if (filter == 'all') {
                $('.group-filter').show();
                groupCheckAllReflect();
            
            } else {
                __sel_institute   = filter;
                filter=parseInt(filter);
                $('.group-filter').hide();
                $("[data-gpfilter=" + filter + "]").show();
                processCheckboxChecked();
            }
            $('#filter_dropdown_text').html($('#gfilter_' + filter).text() + '<span class="caret"></span>');
        
        }

        function processCheckboxChecked() {
            $('#groupAll').prop('checked', !($('[data-gpfilter="'+__sel_institute+'"] .group-course').not(':checked').length));
        }
        
        $(document).on('click', '.group-course', function() {
            __inst_selected = new Array();
            var user_id = $(this).val();
            if ($(this).is(':checked')) {
                $('.list-button').removeClass('list-disabled');
                __group_selected.push(user_id);
            } else {
                $('.list-button').addClass('list-disabled');
                removeArrayIndex(__group_selected, user_id);
            }
            group_count();
        });

        function selectAllGroup(selectAll, group) {

            var selectAllBtn    = $('#' + selectAll);
            var group_list      = $('.' + group+':visible');
            if (selectAllBtn.is(":checked")) {
                group_list.each(function() { //loop through each checkbox
                    var group_id = $(this).val();
                     if ($.inArray(group_id, __group_selected) == -1) {
                    __group_selected.push(group_id);
                    }
                    
                    $(this).prop('checked', true); //check
                });
                // __group_selected=$.unique(__group_selected);
            } else {
                group_list.each(function() { //loop through each checkbox
                    var group_id = $(this).val();
                    removeArrayIndex(__group_selected, group_id);
                    $(this).prop('checked', false); //uncheck
                });
                
                
            }
            groupCheckAllReflect();
            group_count();
        }

        function group_count(){

            if (__group_selected.length > 0) {
                $("#count_reflect").html(__group_selected.length);
            } else {
                $("#count_reflect").html('0');
            }
            groupCheckAllReflect();
        }

        function groupCheckAllReflect(){
            var checkedCount    = $('.group-course:visible:checked').length;
            var total           = $('.group-course:visible').length
            if(total==checkedCount){
                $('#groupAll').prop('checked', true);
            }else if(total>checkedCount){
                $('#groupAll').prop('checked', false);
            }
        }
   
$(document).on('click', '#add_override', function(){
    __assign_option = 0;
    $(".group-course").each(function() { 
        $(this).prop('checked', false);
    });
    $("#end-date").val('');
    $("#attempts").val('0');
    $("#grace-period").val('');
    __assign_option = 0;
    $(".close").trigger('click');
    filter_ins_by('all');
    $("#count_reflect").html('0');
    $("#groupAll").prop('checked', false);
    __group_selected = [];
    __sel_institute ='';
});
$(document).on('click', '#batch-modal', function(){
    var batches                     = new Array();
    var override_batch              = new Array();
    $("#batch-input .group-course:checked").each(function() {
        override_batch.push($(this).val());
    });
    $('#popUpMessage').remove();
    if(override_batch.length==0){
        errorMessage = 'Please select atleast one batch for override<br />';
        $('#over-insit .modal-body').prepend(renderPopUpMessage('error', errorMessage));
        scrollToTopOfPage();
    } else {
        $("#step1_close").trigger('click');
        $('#batchModal').modal();
    }
    
});
$(document).on('click', '#save-override', function() {
    var errorCount                  = 0;
    var errorMessage                = '';
    var batches                     = new Array();
    var end_date                    = $("#end-date").val();
    var attempts                    = $("#attempts").val();
    var grace_period                = $("#grace-period").val();
    var grace_period_type           = $("#grace-period-type").val();
    var override_batch              = [];
    $("#batch-input .group-course:checked").each(function() {
        override_batch.push($(this).val());
        
    });

    if(end_date==''){
        errorMessage += 'Please enter the last date<br />';
        errorCount++;
    }
//alert('batchModal .modal-body');
    $('#popUpMessage').remove();
    if (errorCount > 0)
    {
        $('#batchModal .modal-body').prepend(renderPopUpMessage('error', errorMessage));
        scrollToTopOfPage();
    } 
    else
    {
            $.ajax({
            url: admin_url + 'coursebuilder/save_lecture_override',
            type: "POST",
            data: {"is_ajax": true,"lecture_id":__lecture_id,"last_date":end_date,"attempts":attempts,"grace_period":grace_period,"grace_period_type": grace_period_type,"override_batch":override_batch,"assign_option":__assign_option},
            success: function (response) {
                var data = $.parseJSON(response);
                if(data['error']==false)
                {
                    var override_arr = data['groups'].split(",");
                    var grace_period = '';
                    switch(data['period_type']) {
                        case "1":
                            grace_period = 'Days';
                            break;
                        case "2":
                            grace_period = 'Hours';
                            break;
                        case "3":
                            grace_period = 'Mintutes';
                            break;
                    }
                    $("#no-override").remove();
                    var attempts = (data['attempts']!=0)?data['attempts']:'Unlimited';
                    var renderOverrideHtml = '';
                    if(data['exist']==0){
                        renderOverrideHtml    += '<div class="overrided-rules" id="override_rule_'+data['id']+'">';
                    }
                    renderOverrideHtml    += '<div class="overrided-info">';
                    for (var k in override_arr){
                     renderOverrideHtml    += '<span class="overided-chips">'+override_arr[k]+'</span>';
                    }
                    renderOverrideHtml    += '</div>';
                    renderOverrideHtml    += '<div class="row"><div class="col-md-6"><label>Last Date  :<span class="green-text">'+data['end_date']+'</span></label></div>';
                    renderOverrideHtml    += '<div class="col-md-6"><label>Grace Period  :<span class="green-text">'+data['period']+' '+grace_period+'</span></label></div></div>';
                    // renderOverrideHtml    += '<div class="col-md-6"><label>Attempts  :<span class="green-text">'+attempts+'</span></label></div></div>';
                    renderOverrideHtml    += '<div class="row">';
                    renderOverrideHtml    += '<div class="col-md-12"><button type="button" class="btn btn-success pull-right" data-toggle="modal" data-target="#over-insit" onclick="editOverride('+data['id']+')">Edit</button><button type="button" class="btn btn-danger pull-right"  onclick="deleteOverride('+data['id']+')">Delete</button></div></div>';
                    if(data['exist']==0){
                    renderOverrideHtml    += '</div>';
                    }
                    if(data['exist']==0){
                    $("#override_info").append(renderOverrideHtml);
                    } else {
                    $("#override_rule_"+data['id']).html(renderOverrideHtml);
                    }
                    $("#step2_close").trigger('click');
                   // $("#batchModal").hide();
                } 
            }
           });
    }
});

function editOverride(id){
    $(".group-course").each(function() { 
    $(this).prop('checked', false);
    });
    $("#count_reflect").html('0');
    $(".close").trigger('click');
    filter_ins_by('all');
    $("#groupAll").prop('checked', false);
    __group_selected = [];
    __sel_institute ='';
    __assign_option = id;
    $.ajax({
            url: admin_url + 'test_manager/get_assessment_override',
            type: "POST",
            data: {"is_ajax": true,"id":id},
            success: function (response) {
                var data = $.parseJSON(response);
                $("#count_reflect").html('0');
                $('.group-course').prop('checked', false);
                $.each(data['override_batches'], function (key,val) {
                    var value = val.trim();
                 $('#gr_course_'+value).trigger("click");
                });
                $("#count_reflect").html(data['override_batches'].length);
                var numItems = $('.group-course').length;
                if (data['override_batches'].length == numItems) {
                    $('#groupAll').prop('checked', true);
                } else {
                    $('#groupAll').prop('checked', false);
                }
                $("#end-date").val(data['override_detail']['lo_end_date']);
                $("#attempts").val(data['override_detail']['lo_attempts']);
                $("#grace-period").val(data['override_detail']['lo_period']);
                $("#grace-period-type").val(data['override_detail']['lo_period_type']);
            }
     });
}
    
    </script>
<!-- Initialize the plugin: -->
    <script>
    $(function(){
        var today = new Date();
        $("#end-date").datepicker({
            language: 'en',
            minDate: today,
            dateFormat: 'dd-mm-yy',
            autoClose: true
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
    function deleteOverride(id){
                var messageObject = {
                'body':'Are you sure to delete this override.',
                'button_yes':'CONTINUE', 
                'button_no':'CANCEL',
                'continue_params':{'id':id}
            }; 
            callback_warning_modal(messageObject, deleteOverrideConfirmed);
    }
    function deleteOverrideConfirmed(params){
        var id =params.data.id;
        $.ajax({
                url: admin_url + 'test_manager/delete_assessment_override',
                type: "POST",
                data: {"is_ajax": true,"id":id},
                success: function (response) {
                    var data = $.parseJSON(response);
                    $("#override_rule_"+id).remove();
                    $("#advanced_confirm_box_cancel").trigger('click');
                    alert($("#overrided-rules").length());
                    if ($('#override_info').is(':empty')){
                        var renderOverrideHtml = '';
                        renderOverrideHtml    += '<div class="overrided-rules" id="no-override">';
                        renderOverrideHtml    += ' <div class="row" style="text-align:center;"><p >No override yet.</p></div>';  
                        renderOverrideHtml    += '</div>';   
                    }
                    $('#overrided-rules').html(renderOverrideHtml);
                    
                }
        });
    }
    </script>
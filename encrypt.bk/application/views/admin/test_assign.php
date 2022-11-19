<?php include_once 'coursebuilder/lecture_header.php';?>

<!-- ############################# --> <!-- END -->
<link rel="stylesheet" href="<?php echo assets_url() ?>css/datepicker.min.css">
<link rel="stylesheet" href="<?php echo assets_url() ?>css/jquery.timepicker.min.css">
<link rel="stylesheet" href="<?php echo assets_url() ?>css/jquery-ui.css">


<style>
        .addtest-container div:nth-child(2) {
              border-top: solid 1px #ccc;
            border-bottom: none;
        }
        .custom-width{
            max-width:780px;
        }    
        .overrided-info p{
            font-size: 14px;
            font-weight: 600;
            padding: 0 15px;
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
            margin: 15px 0;
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
        .inside-box-padding {
            padding: 5px 30px;
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
         .override-dropdown li a {
            text-overflow: ellipsis;
            overflow: hidden;
         }
         .custom-select.select-group{
            height: 100%;
            border: none;
            background: #eeeeee;
         }
         .
        select:hover,select:active,select:focus 
        {
            outline:none !important;
        }
        .rTable.content-nav-tbl .rTableRow > .rTableCell {
            min-width: 240px;
        }
        .chk-box{
            display: inline-flex;
            min-width: 180px;
            text-overflow: ellipsis;
            overflow: hidden;
            white-space: nowrap;
        }
        .inside-box .checkbox-wrap {
            padding: 5px 0;
        }
        #announcement_batch_select{
            max-height: 220px;
            overflow-y: auto;
            max-width: 275px;
        }
        .overided-chips{
            border: 1px solid #470871;
            border-radius: 16px;
            padding: 0px 10px;
            line-height: 25px;
            margin-right: 10px;
            display: inline-block;
        }
        .ui-timepicker-wrapper{
            position: fixed !important;
        }
</style>
<!--Tag input js-->

<!-- MAIN TAB --> <!-- STARTS -->
<?php include_once('test_header.php'); ?>
<div class="right-wrap small-width base-cont-top-heading container-fluid pull-right rightwrap-top-update">
    <br/>
    <div class="list-group test-listings">
      <a href="javascript:void(0)" class="list-group-item active">
        <span class="font15">Instructions</span>
      </a>
      <a href="javascript:void(0)" class="list-group-item link-style">
          <span class="green-span"><i class="icon icon-ok-circled"></i></span>          
          <span class="listing-text">Batch override means to change the quiz setting for a particular batch of students (i.e. institution wise and batch-wise).</span>        
      </a>
      <a href="javascript:void(0)" class="list-group-item link-style">
          <span class="green-span"><i class="icon icon-ok-circled"></i></span>          
          <span class="listing-text">Batch override is applicable on changing the settings such as quiz duration, date and time of quiz and number of attempts.</span>             
      </a>
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

                        <?php // include_once('messages.php') ?>
                       

       <div class="container custom-width overrider">
        <div class="row" id="override-elements">
            <?php
            if(!empty($override_details)){
            foreach($override_details as $override_detail){
                ?>
                <div class="overrided-rules override_boxes" id="override_rule_<?php echo $override_detail['id']; ?>">
                    <!-- input row 1 -->
                    <div class="row">
                        <div class="overrided-info">
                           <p><?php 
                           $override_groups = explode(",",$override_detail['groups']);
                           foreach($override_groups as $override_group) {
                             echo '<span class="overided-chips">'.$override_group.'</span>';
                           }?></p>
                        </div>
                        <div class="col-md-6">
                            <label>Start Date & Time: <span class="green-text"><?php if($override_detail['start_date']!=NULL){ echo date("d-m-Y", strtotime($override_detail['start_date'])).' '.$override_detail['start_time']; } ?></span></label>
                        </div>
                        <div class="col-md-6">
                            <label>End Date & Time: <span class="green-text"><?php if($override_detail['end_date']!=NULL){ echo date("d-m-Y", strtotime($override_detail['end_date'])).' '.$override_detail['end_time']; } ?></span></label>
                        </div>
                    </div>

                    <!-- row ends -->
                    <!-- input row 2 -->
                    <div class="row">
                        <div class="col-md-6">
                            <label>Duration: <?php echo $override_detail['duration']; echo $override_detail['duration'] > 1 ? ' Minutes' : ' Minute';?></label>
                        </div>
                        <div class="col-md-6">
                            <label>Grace Period: <?php echo $override_detail['period']; ?> 
                            <?php if($override_detail['period_type']==1){ echo 'Days'; }
                                else if($override_detail['period_type']==2){ echo 'Hours'; } 
                                else if($override_detail['period_type']==3){ echo 'Minute(s)'; } 
                            ?> </label>
                        </div>
                    </div>
                    <!-- row ends -->
                    <div class="row">
                        <div class="col-md-6">
                            <label>Attempts: <?php if($override_detail['attempts']==0){ echo 'Unlimited'; } else { echo $override_detail['attempts']; }; ?></label>
                        </div>
                    </div>
                    <div class="row">
                    <button type="button" class="btn btn-success pull-right" data-toggle="modal" data-target="#over-insit" onclick="editOverride(<?php echo $override_detail['id']; ?>)">Edit</button>
                    <button type="button" class="btn btn-danger pull-right"  onclick="deleteOverride(<?php echo $override_detail['id']; ?>)">Delete</button>
                    </div>
            </div>
                <?php
            }
            } else {
                ?>
                <div class="overrided-rules">
                    <div class="row" style="text-align:center;"><p >No override yet.</p></div>
                </div>
                <?php
            }
            ?>
            
            </div>
            <!-- modal starts -->
            <div class="modal-btn">
                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#over-insit" id="add-override">Override by Batch</button>
            </div>
            <!-- modals -->

        
    </div>

    <!-- override modals -->
    <div class="modal fade in" id="over-insit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-small" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" class="icon icon-cancel-1"></span>
                    </button>
                    <h4 class="modal-title" id="create_box_title">Override</h4>
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
                                                        <label>
                                                            <input onclick="selectAllGroup('groupAll', 'group-course')" value="1" class="select-users-new-group-parent" id="groupAll" type="checkbox"> Select All (<span id="count_reflect">0</span>/<?php echo count($course_groups); ?>)</label>
                                                    </a>
                                                </div>
                                                <div class="rTableCell dropdown" style="min-width:295px;right:0px;">
                                       
                                                    <a href="javascript:void(0)" class="dropdown-toggle min-width115" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" id="filter_dropdown_text">Select Institution<span class="caret"></span></a>
                                                    <ul class="dropdown-menu white override-dropdown" id="announcement_batch_select">
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
                                                //print_r($course_groups);
                                                // die();
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
                        <button class="btn btn-green pull-right add-continue" data-step="2" data-action="add" data-canid="" data-toggle="modal" id="batch-modal" >CONTINUE</button>
                    <!-- </a> -->
                    <!-- <a href=""> -->
                        <button type="button" class="btn btn-red" data-dismiss="modal">CANCEL</button>
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
                    <h4 class="modal-title" id="create_box_title">Quiz Group Override</h4>
                </div>
                <div class="modal-body">
                <?php
                //print_r($test);
                 
                ?>
                    <!-- input row 1 -->
                    <div class="row input-padding">
                        <div class="col-md-6">
                            <label>Start Date</label>
                            <input type="text" class="form-control" name="start-date"  id="start-date" <?php if(isset($test['a_from'])){ ?>value="<?php echo date("d-m-Y", strtotime($test['a_from'])); ?>" <?php } ?> placeholder="dd-mm-yyyy" readonly="" style="background: #fff;">
                            <!--input type="text" placeholder="Enter the start date" class="form-control hasDatepicker" id="start-date" autocomplete="off"-->
                        </div>
                        <div class="col-md-6" >
                            <label>Start Time</label>
                            <span id="start-time-container">
                                <input type="text" placeholder="Enter the start time" class="form-control timepicker timepicker-with-dropdown" id="start-time" <?php if(isset($test['a_from_time'])){ ?>value="<?php echo $test['a_from_time']; ?>" <?php } ?> autocomplete="off" onkeydown="event.preventDefault()">
                            </span>
                        </div>
                    </div>
                    <!-- row ends -->
                    <!-- input row 2 -->
                    <div class="row input-padding">
                        <div class="col-md-6">
                            <label>End Date</label>
                            <input type="text" placeholder="dd-mm-yyyy" id="end-date"  class="form-control" autocomplete="off" <?php if(isset($test['a_to'])){ ?>value="<?php echo date("d-m-Y", strtotime($test['a_to'])); ?>" <?php } ?> readonly="" style="background: #fff;">
                        </div>
                        <div class="col-md-6">
                            <label>End Time</label>
                            <span id="end-time-container">
                              <input type="text" placeholder="Enter the end time" id="end-time" class="form-control timepicker timepicker-with-dropdown" autocomplete="off" <?php if(isset($test['a_to_time'])){ ?>value="<?php echo $test['a_to_time']; ?>" <?php } ?> onkeydown="event.preventDefault()">
                            </span>
                        </div>
                    </div>
                    <!-- row ends -->
                    <!-- input row 3 -->
                    <div class="row input-padding">
                        <div class="col-md-6">
                            <label>Duration (In Min.)</label>
                            <div class="input-group">
                                            <input type="number"  oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="3" min="0" name="duration" onkeypress="return isNumber(event)" id="duration" class="form-control" <?php if(isset($test['a_duration'])){ ?>value="<?php echo $test['a_duration']; ?>" <?php } ?> placeholder="Eg: 30" aria-describedby="basic-addon1" data-validation="number">
                                            <span class="input-group-addon" id="basic-addon1">MIN</span>
                            </div>
                            <!--input type="text" class="form-control" 
                            placeholder="Enter the duration" id="duration" autocomplete="off"-->
                        </div>
                        <div class="col-md-6">
                            <label>Number of Attempts (0 for unlimited)</label>
                            <input type="number" min="0" id="attempts"  oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="3"  class="form-control" onkeypress="return isNumber(event)" placeholder="Eg : 1,5,10.." <?php if(isset($test['a_total_attempt'])){ ?>value="<?php echo $test['a_total_attempt']; ?>" <?php } else {?>value="0"<?php } ?> autocomplete="off">
                        </div>
                    </div>
                    <!-- grace marks -->
                    <div class="row input-padding">
                        <div class="col-md-6">
                            <label>Grace Period</label>
                                <div class="input-group">
                                    <input type="number"  oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="3" min="0" class="form-control cb_validity" name="cb_validity" onkeypress="return isNumber(event)" id="grace-period" placeholder="Eg: 180" aria-describedby="basic-addon1">
                                    <span class="input-group-addon no-padding" id="basic-addon1">
                                        <select class="custom-select select-group" id="grace-period-type"  style="" >
                                            <option value="1">Days</option>
                                            <option value="2">Hours</option>
                                            <option value="3">Minutes</option>
                                        </select>
                                    </span>
                                </div>
                        </div>
                        <div class="col-md-6"></div>
                    </div>
                    <!-- row ends -->
                </div>
                <div class="modal-footer">
                    <button class="btn btn-green pull-right add-continue" data-step="1" id="save-override">SAVE</button>
                    <button type="button" class="btn btn-red" data-dismiss="modal">CANCEL</button>
                </div>
            </div>
        </div>
    </div>
    <!-- batch modal ends -->

        </div>
        <!-- =========================== -->
        <!-- Nav section inside this wrap  --> <!-- END -->
    </div>
    <!-- ==========================  -->
    <!--  LEFT CONTENT--> <!-- ENDS -->
</section>

<!-- JS -->

<script src="<?php echo assets_url() ?>js/bootstrap-tagsinput.js" type="text/javascript"></script>
<!-- <script type="text/javascript" src="<?php echo assets_url() ?>js/jquery.timepicker.js"></script> -->

<!-- <script type="text/javascript" src="<?php echo assets_url() ?>js/bootstrap-multiselect.js"></script> -->
    <script type="text/javascript" src="<?php echo assets_url() ?>js/jquery-ui.min.js"></script>
    <script type="text/javascript" src="<?php echo assets_url() ?>js/jquery.timepicker.min.js"></script>
    <script>
      var __start_date       = '<?php echo (isset($test['a_from']))?date("d-m-Y", strtotime($test['a_from'])):null; ?>';
      var __end_date         = '<?php echo (isset($test['a_to']))? date("d-m-Y", strtotime($test['a_to'])):null; ?>';
      var __start_time       = '<?php echo (isset($test['a_from_time']))?$test['a_from_time']:''; ?>';
      var __end_time         = '<?php echo (isset($test['a_to_time']))?$test['a_to_time']:''; ?>';
      var __duration         = '<?php echo (isset($test['a_duration']))?$test['a_duration']:'0'; ?>';
      var __attempts         = '<?php echo (isset($test['a_total_attempt']))?$test['a_total_attempt']:'0'; ?>';
     var __assign_option   = 0;
      var __lecture_id   = '<?php echo $test['id'] ?>';
      var __inst_selected   = new Array();
      var __group_selected  = new Array();
      var __sel_institute   = '';
      var __fullGroupCount  = '';
      var __admin_url       = '<?php echo admin_url() ?>';
      $('#attempts,#duration,#grace-period').bind("cut copy paste",function(e) {
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
            if((__sel_institute=="")||(__sel_institute==null)){
                __group_selected = [];
            }
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
     
$(document).on('click', '#add-override', function(){
    $("#start-date").val(__start_date);
    $("#start-time").val(__start_time);
    $("#end-date").val(__end_date);
    $("#end-time").val(__end_time);
    $("#duration").val(__duration);
    $("#attempts").val(__attempts);
    __assign_option = 0;
    $(".group-course").each(function() { 
    $(this).prop('checked', false);
    });
    $("#count_reflect").html('0');
    $(".close").trigger('click');
    filter_ins_by('all');
    $("#groupAll").prop('checked', false);
    __group_selected = [];
    __sel_institute ='';
});
$(document).on('click', '#batch-modal', function(){
    //var batches                     = new Array();
    var override_batch              = new Array();
    $("#batch-input .group-course:checked").each(function() {
        override_batch.push($(this).val());
    });
    $('#popUpMessage').remove();
    //alert(override_batch.length);
    if(override_batch.length==0){
        errorMessage = 'Please select atleast one batch for override<br />';
        $('#over-insit .modal-body').prepend(renderPopUpMessage('error', errorMessage));
        scrollToTopOfPage();
    } else {
        $('#over-insit').modal('hide');
        $('#batchModal').modal('show');
    }
    
});
$(document).on('click', '#save-override', function() {
    var errorCount                  = 0;
    var errorMessage                = '';
    var batches                     = new Array();
    var startDate                   = $('#start-date').val();
    var endDate                     = $("#end-date").val();
    if(startDate!=''){
        start_date                  = toDate(startDate);
    } else {
        start_date                  = null;
    }

    if(endDate!=''){
        end_date                  = toDate(endDate);
    } else {
        end_date                  = null;
    }
    
    var start_time                  = $("#start-time").val();
    //var end_date                    = toDate($("#end-date").val());
    var end_time                    = $("#end-time").val();
    var duration                    = $("#duration").val();
    var attempts                    = $("#attempts").val();
    var grace_period                = $("#grace-period").val();
    var grace_period_type           = $("#grace-period-type").val();
    var override_batch              = [];
    $("#batch-input .group-course:checked").each(function() {
        override_batch.push($(this).val());
        
    });

    var today = new Date();
    today.setHours(0,0,0,0);
    if(start_date!=null){
        start_date.setHours(0,0,0,0);
    }
    if(end_date!=null){
        end_date.setHours(0,0,0,0);
    }
    
   

    // if(start_date < today){
    //     errorMessage += 'Start date should not be less than current date.<br />';
    //     errorCount++;
    // }

    // if(end_date < today){
    //     errorMessage += 'End date should not be less than current date.<br />';
    //     errorCount++;
    // }

    if(start_date==null){
        errorMessage += 'Please enter the start date.<br />';
        errorCount++;
    }

    if(end_date!=null){
        if(start_date > end_date){
            errorMessage += 'End date should be greater than start date.<br />';
            errorCount++;
        }
    }

    if((start_date!=null)&&(end_date!=null)){
        start_time                  = $("#start-time").val();
        end_time                    = $("#end-time").val();
        if(start_date-end_date === 0){
        var aDate= convertHoursformat(start_time.toLowerCase());
        var bDate = convertHoursformat(end_time.toLowerCase());
        
            if(aDate>=bDate){
                errorMessage += 'End time should be greater than start time.<br />';
                errorCount++;
            }
        }
    }

    if(start_date!=null){
        if(start_time==''){
            errorMessage += 'Please enter the start time<br />';
            errorCount++;
        }
    }

    if(end_date!=null){
        if(end_time==''){
            errorMessage += 'Please enter the end time<br />';
            errorCount++;
        }
    }

    if(duration==''){
        errorMessage += 'Please enter the duration<br />';
        errorCount++;
    }

    if(attempts==''){
        errorMessage += 'Please enter the attempts<br />';
        errorCount++;
    }

    $('#popUpMessage').remove();
    if (errorCount > 0)
    {
        $('#batchModal .modal-body').prepend(renderPopUpMessage('error', errorMessage));
        scrollToTopOfPage();
    } 
    else
    {
            $.ajax({
            url: __admin_url + 'test_manager/save_assessment_override',
            type: "POST",
            data: {"is_ajax": true,"lecture_id":__lecture_id,"start_date":startDate,"end_date":endDate,"start_time":start_time,"end_time":end_time,"duration":duration,"attempts":attempts,"grace_period":grace_period,"grace_period_type": grace_period_type,"override_batch":override_batch,"assign_option":__assign_option},
            success: function (response) {
                var data = $.parseJSON(response);
                //console.log(data);
                if(data['error']==false)
                {
                    location.reload();
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
    //filter_ins_by('all');
    $("#groupAll").prop('checked', false);
    __group_selected = [];
    __sel_institute ='';
    __assign_option = id;
    $.ajax({
            url: __admin_url + 'test_manager/get_assessment_override',
            type: "POST",
            data: {"is_ajax": true,"id":id},
            success: function (response) {
                var data = $.parseJSON(response);
                
                $("#count_reflect").html('0');
                $('.group-course').prop('checked', false);
                
                // $.each(data['override_batches'], function (key,val) {
                //     var value = val.trim();
                //  $('#gr_course_'+value).trigger("click");
                // });
                // $("#count_reflect").html(data['override_batches'].length);
                // var numItems = $('.group-course').length;
                // if (data['override_batches'].length == numItems) {
                //     $('#groupAll').prop('checked', true);
                // } else {
                //     $('#groupAll').prop('checked', false);
                // }

                $(".group-course").each(function() {

                    if ($.inArray($(this).val(), data['override_batches']) != -1) {
                        __group_selected.push($(this).val());
                        $(this).attr('checked', 'checked');
                        $(this).prop("checked", true);
                    }
                });

                if(__fullGroupCount==__group_selected.length){
                    $('#groupAll').prop('checked', true);
                }else{
                    $('#groupAll').prop('checked', false);
                }
                if (__group_selected.length > 0) {
                    $("#count_reflect").html(__group_selected.length);
                } else {
                    $("#count_reflect").html('0');
                }
                $("#start-date").val(data['override_detail']['lo_start_date']);
                $("#end-date").val(data['override_detail']['lo_end_date']);
                $("#start-time").val(data['override_detail']['lo_start_time']);
                $("#end-time").val(data['override_detail']['lo_end_time']);
                $("#duration").val(data['override_detail']['lo_duration']);
                $("#attempts").val(data['override_detail']['lo_attempts']);
                $("#grace-period").val(data['override_detail']['lo_period']);
                $("#grace-period-type").val(data['override_detail']['lo_period_type']);
            }
     });
}
    
    </script>
<!-- Initialize the plugin: -->
    <script>
    function toDate(dateStr) {
        var parts = dateStr.split("-")
        return new Date(parts[2], parts[1] - 1, parts[0])
    }
    /*$(function(){
        var today = new Date();
        $("#start-date").datepicker({
            language: 'en',
            minDate: today,
            dateFormat: 'dd-mm-yy',
            autoClose: true,
            defaultDate: null
        });
    });

/** date picker ends, please dont remove the above code */

    function convertHoursformat(time){
        var hours = Number(time.match(/^(\d+)/)[1]);
        var minutes = Number(time.match(/:(\d+)/)[1]);
        //var AMPM = time.match(/\s(.*)$/)[1];
        var AMPM = time.slice(-2);
        if(AMPM == "pm" && hours<12) hours = hours+12;
        if(AMPM == "am" && hours==12) hours = hours-12;
        var sHours = hours.toString();
        var sMinutes = minutes.toString();
        if(hours<10) sHours = "0" + sHours;
        if(minutes<10) sMinutes = "0" + sMinutes;
        var format = sHours + ":" + sMinutes;
        return format;
    }
    $(document).ready(function(){
            $('input.timepicker').timepicker({});
                __fullGroupCount = $('.group-course').length;
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
        var id = params.data.id;
        $.ajax({
                url: __admin_url + 'test_manager/delete_assessment_override',
                type: "POST",
                data: {"is_ajax": true,"id":id},
                success: function (response) {
                    var data = $.parseJSON(response);
                    $("#override_rule_"+id).remove();
                    $("#advanced_confirm_box_cancel").trigger('click');
                    if($(".override_boxes").length < 1)
                    {
                       var rendarHtml = '<div class="overrided-rules">';
                           rendarHtml += '<div class="row" style="text-align:center;"><p >No override yet.</p></div>';
                           rendarHtml += '</div>';
                        $("#override-elements").html(rendarHtml);
                    }
                   
                }
        });
    }

    $(document).ready(function () {
    $("#start-date").datepicker({
        dateFormat: "dd-mm-yy",
        minDate: 0,
        onSelect: function (dateText, inst) {
            var sel_date            = new Date(dateText);
            var today_date          = new Date();
            var today_date_second   = new Date(today_date.getFullYear(),today_date.getMonth(),today_date.getDate(),0,0,0,0);
            
            if(sel_date.getDate() == today_date_second.getDate()){
                    var current_time = today_date.getHours();
                    $('#start-time').remove();
                    $('#start-time-container').prepend('<input type="text" placeholder="Enter the start time" class="form-control timepicker timepicker-with-dropdown ui-timepicker-input" id="start-time" value="" autocomplete="off" onkeydown="event.preventDefault()">');
                    $('#start-time').timepicker({
                        timeFormat: 'h:i A',
                        interval: 60,
                        minTime: (current_time+1).toString(),
                        maxTime: '11:30pm',
                        dynamic: false,
                        dropdown: true,
                        
                    });
                }else{

                    $('#start-time').remove();
                    $('#start-time-container').prepend('<input type="text" placeholder="Enter the start time" class="form-control timepicker timepicker-with-dropdown ui-timepicker-input" id="start-time" value="" autocomplete="off" onkeydown="event.preventDefault()">');
                    $('#start-time').timepicker({
                        timeFormat: 'h:i A',
                        interval: 60,
                        maxTime: '11:30pm',
                        dynamic: false,
                        dropdown: true,
                    });
                }

            var dt2 = $('#end-date');
            var startDate = $(this).datepicker('getDate');
            startDate.setDate(startDate.getDate() + 30);
            var minDate = $(this).datepicker('getDate');
            var dt2Date = dt2.datepicker('getDate');
            var dateDiff = (dt2Date - minDate)/(86400 * 1000);
            if (dt2Date == null || dateDiff < 0) {
                    dt2.datepicker('setDate', minDate);
            }
            else if (dateDiff > 30){
                    dt2.datepicker('setDate', startDate);
            }
            dt2.datepicker('option', 'maxDate', startDate);
            dt2.datepicker('option', 'minDate', minDate);
        }
    });

    $('#end-date').datepicker({
        dateFormat: "dd-mm-yy",
        minDate: 0,
        onSelect: function(dateText, inst) {
                var sel_date            = new Date(dateText);
                var today_date          = new Date();
                var today_date_second   = new Date(today_date.getFullYear(),today_date.getMonth(),today_date.getDate(),0,0,0,0);

                if(sel_date.getDate() == today_date_second.getDate()){

                    var current_time = today_date.getHours();
                    $('#end-time').remove();
                    $('#end-time-container').prepend('<input type="text" placeholder="Enter the end time" id="end-time" class="form-control timepicker timepicker-with-dropdown ui-timepicker-input" autocomplete="off" value="" onkeydown="event.preventDefault()">');
                    $('#end-time').timepicker({
                        timeFormat: 'h:i A',
                        interval: 60,
                        minTime: (current_time+1).toString(),
                        maxTime: '11:30pm',
                        dynamic: false,
                        dropdown: true,
                    });
                }else{

                    $('#end-time').remove();
                    $('#end-time-container').prepend('<input type="text" placeholder="Enter the end time" id="end-time" class="form-control timepicker timepicker-with-dropdown ui-timepicker-input" autocomplete="off" value="" onkeydown="event.preventDefault()">');
                    $('#end-time').timepicker({
                        timeFormat: 'h:i A',
                        interval: 60,
                        maxTime: '11:30pm',
                        dynamic: false,
                        dropdown: true,
                    });
                }
            }
    });
});
    
    </script>

<?php include_once 'footer.php';?>
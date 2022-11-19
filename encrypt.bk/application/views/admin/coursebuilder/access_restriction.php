<link rel="stylesheet" href="<?php echo assets_url() ?>css/datepicker.min.css">
<link rel="stylesheet" href="<?php echo assets_url() ?>css/jquery.timepicker.min.css">
<link rel="stylesheet" href="<?php echo assets_url() ?>css/jquery-ui.css">
<style>
.activity-count-label{
    padding: 5px 0 5px 15px;
}
.lecture-dropdown-wrapper{
    padding: 0 0 0 15px;
}
.close-activity-btn{
    font-size: 26px;
    font-weight: 800;
    color: #f70000;
    cursor: pointer;
    user-select: none;
}
.add-activity-btn{
    font-size: 26px;
    font-weight: 800;
    color: #09bf63;
    cursor: pointer;
    user-select: none;
}
.activity-selector{
    min-width: 100px;
    background: #fff;
    border: 1px solid #ccc;
    border-radius: 4px;
    padding: 6px;
}
.add-new-activity-btn,  .add-new-activity-btn:focus {
    color: #53b665;
    font-weight: 600;
}
.add-new-activity-btn:hover {
    color: #53b665;
    font-weight: 600;
    text-decoration: underline;
}
.allow_edit{
    background-color:#fff !important;
}
</style>
<div class="panel-heading">
    <h4 class="panel-title">
    <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseAccessRestriction">
        <h4 class="coursebuilder-settingstab-title">Access Restriction</h4>
    </a>
    </h4>
</div>
<?php

//print_r($lecture); die;//current_lecture_id id
$verify_option                  = array( 'all' => 'All', 'anyone' => 'Anyone' );
$restriction                    = $lecture['cl_access_restriction'];
$restriction_verify             = isset($restriction['verify'])?$restriction['verify']:'';
$restriction_course_percentage  = isset($restriction['course_percentage'])?$restriction['course_percentage']:array();
$restriction_available_from     = isset($restriction['available_from'])?$restriction['available_from']:array();
$restriction_available_till     = isset($restriction['available_till'])?$restriction['available_till']:array();
$restriction_activities         = isset($restriction['activities'])?$restriction['activities']:array();
?>
<div id="collapseAccessRestriction" class="panel-collapse collapse">
    <form id="access_restriction_form" onsubmit="return validateForm()" method="POST" action="<?php echo admin_url('coursebuilder/save_access_restriction') ?>">
        <div class="panel-body">
            <div class="builder-inner-from">

                <?php  $checked = 'checked="checked"'; ?>
                <div class="form-group clearfix" style="display:none;">
                    <div class="col-sm-9 no-padding">
                        <label class="mb10"><?php echo lang('access') ?>* :</label>
                        <br>
                        <label class="pad-top10 pad-right20">
                            <input type="radio" name="cl_limited" <?php echo (($lecture['cl_limited_access']==0)?$checked:''); ?> value="0"> Unlimited Access
                        </label>
                        <label>
                            <input type="radio" name="cl_limited" <?php echo (($lecture['cl_limited_access'] > 0)?$checked:''); ?>  value="1" > Limited Access
                        </label>
                    </div>

                    <?php 
                    $disabled = 'disabled="disabled"';    
                    if(isset($lecture['cl_limited_access']) && $lecture['cl_limited_access'] > 0)
                    {
                        $disabled = '';
                    }
                    ?>
                    <div class="col-sm-3 no-padding">
                        <div class="form-group">
                            <label class="mb10">Limit&nbsp;<small>(attempt)</small> * :</label>
                            <input type="text" <?php echo $disabled ?> class="form-control text-center" maxlength="3" name="cl_limited_access" placeholder="Eg: 5" value="<?php echo ($lecture['cl_limited_access']!=0)?$lecture['cl_limited_access']:null ?>">
                        </div>
                    </div>
                </div>


                <?php 
                $checked = '';
                $disabled = 'disabled="disabled"';
                if(isset($restriction_course_percentage['active']) && $restriction_course_percentage['active']=='1')
                {
                    $checked = 'checked="checked"';
                    $disabled = '';    
                }
                ?>
                <div class="form-group clearfix">
                    <div class="col-sm-9 no-padding">
                        <label>                            
                            <input type="checkbox" value="1" <?php echo $checked ?> name="restriction[course_percentage][active]">
                            Percentage of Course Completion
                        </label>
                    </div>
                    <div class="col-sm-3 no-padding">
                        <input class="form-control text-center" <?php echo $disabled ?> value="<?php echo isset($restriction_course_percentage['percentage'])?$restriction_course_percentage['percentage']:'' ?>" type="text" maxlength="3" name="restriction[course_percentage][percentage]" placeholder="%">
                    </div>
                </div>
                <?php 
                $checked = '';
                $disabled = 'disabled="disabled"';
                $allow_edit = '';
                if(isset($restriction_available_from['active']) && $restriction_available_from['active']=='1')
                {
                    $checked = 'checked="checked"';
                    $disabled = '';
                    $allow_edit = 'allow_edit';    
                }
                ?>
                <div class="form-group clearfix">
                    <div class="col-sm-4 no-padding">
                        <label>
                            <input type="checkbox" id="avail-from" value="1" <?php echo $checked ?> name="restriction[available_from][active]">
                            Available From 
                        </label>
                    </div>
                    <div class="col-sm-5">
                        <input id="date_avail_from" class="form-control text-center <?php echo $allow_edit ?>" <?php echo $disabled ?> value="<?php echo (isset($restriction_available_from['date'])?$restriction_available_from['date']:'') ?>" type="text" autocomplete="off" name="restriction[available_from][date]" placeholder="dd-mm-yyyy" readonly="readonly">
                    </div>
                    <div class="col-sm-3 no-padding" id="from-time-container">
                        <input id="from_time" class="form-control text-center" <?php echo $disabled ?> value="<?php echo (isset($restriction_available_from['time'])?$restriction_available_from['time']:'') ?>" type="text" name="restriction[available_from][time]" placeholder="Time" onkeydown="event.preventDefault()">
                    </div>
                </div>

                <?php 
                $checked = '';
                $disabled = 'disabled="disabled"';
                $allow_edit = '';    
                if(isset($restriction_available_till['active']) && $restriction_available_till['active']=='1')
                {
                    $checked = 'checked="checked"';
                    $disabled = '';    
                    $allow_edit = 'allow_edit';    
                }
                ?>
                <div class="form-group clearfix">
                    <div class="col-sm-4 no-padding">
                    <label>
                        <input type="checkbox" id="avail-to" value="1" <?php echo $checked ?> name="restriction[available_till][active]">
                        Available Till 
                    </label>
                    </div>
                    <div class="col-sm-5">
                    <input id="date_avail_to" class="form-control text-center <?php echo $allow_edit ?>" <?php echo $disabled ?> value="<?php echo (isset($restriction_available_till['date'])?$restriction_available_till['date']:'') ?>" type="text" autocomplete="off" name="restriction[available_till][date]" placeholder="dd-mm-yyyy" readonly="readonly">
                    </div>
                    <div class="col-sm-3 no-padding" id="to-time-container">
                        <input id="to_time" class="form-control  text-center" <?php echo $disabled ?> value="<?php echo (isset($restriction_available_till['time'])?$restriction_available_till['time']:'') ?>" type="text" name="restriction[available_till][time]" placeholder="Time" onkeydown="event.preventDefault()">
                    </div>
                </div>
                <div class="form-group clearfix ">
                    <div class="col-sm-12 no-padding">
                    <label style="font-weight:600;">Students must match 
                            <select name="restriction[verify]" class="activity-selector">
                            <?php foreach($verify_option as $vo_value => $vo_label): ?>
                                <option <?php echo ($restriction_verify == $vo_value ? 'selected="selected"':'') ?> value="<?php echo $vo_value ?>"><?php echo $vo_label ?></option>
                            <?php endforeach; ?>
                            </select>
                    of the following rules</label>
                    </div>
                </div>
                <div class="form-group clearfix" id="activities_wrapper">
                <?php if(!empty($restriction_activities)): ?>
                    <?php $activity_count = 1; ?>
                    <?php foreach($restriction_activities as $key => $activity): ?>
                        <div class="row activities-row" data-id="<?php echo $key ?>" id="activity_row_<?php echo $key ?>">
                            <label class="activity-count-label">Rule <?php echo $activity_count ?></label>
                                <div class="clearfix"></div>
                                <div class="col-sm-5 lecture-dropdown-wrapper">
                                    <select class="form-control lecture-dropdown" data-lecture-id="<?php echo $activity['lecture_id'] ?>" name="restriction[activities][<?php echo $key ?>][lecture_id]"></select>
                                </div>
                                <div class="col-sm-5">
                                    <select class="form-control lecture-action" data-action-id="<?php echo $activity['action'] ?>" name="restriction[activities][<?php echo $key ?>][action]"></select>
                                </div>
                                <div class="col-sm-1 no-padding">
                                    <?php $disabled = ($activity['action'] != 'complete_with_percentage')?'disabled="disabled"':'' ; ?>
                                    <input <?php echo $disabled ?> class="form-control text-center" maxlength="3" value="<?php echo (isset($activity['percentage'])?$activity['percentage']:'') ?>" type="text" name="restriction[activities][<?php echo $key ?>][percentage]">
                                </div>
                                <div class="col-sm-1">
                                    <span onclick="removeActivity('<?php echo $key ?>', '<?php echo $activity_count ?>')" class="close-activity-btn">×</span>
                                </div>
                            <?php $activity_count++; ?>
                            <div class="clearfix"></div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
                </div>

                
                <div class="form-group clearfix ">
                    <div class="col-sm-12 text-center add-rule-btn">
                        <a href="javascript:void(0)" class="add-new-activity-btn" onclick="generateActivity()">Add New Rule</a>
                    </div>
                </div>

                <div class="text-right">
                    <input type="hidden" name="access_lecture_id" value="<?php echo $lecture['id'] ?>">
                    <button type="button" onclick="saveAccessRestriction()" class="btn btn-green"><?php echo lang('save') ?></button>
                </div>
            </div>
        </div>
</form>
</div>

<script type="text/javascript" src="<?php echo assets_url() ?>js/datepicker.js"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/datepicker.en.js"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/jquery.timepicker.min.js"></script>

<script>
    var __activityCount         = Number('<?php echo count($restriction_activities) ?>')+1;
    var __lectureObjectsTemp    = $.parseJSON(atob('<?php echo base64_encode(json_encode(get_course_lectures($lecture['cl_course_id'], $this))); ?>'));
    var __lectureObjects        = {};
    var __lectueTypeObjects     = $.parseJSON(atob('<?php echo base64_encode(json_encode($this->__lecture_types_restriction)); ?>'));
    var __lectureOptionHtml     = '';
        __lectureOptionHtml += '<option value="">Choose Lecture</option>';
        var current_lecture_id = '<?php echo $lecture['id']?>';
        $.each(__lectureObjectsTemp, function(lectureKey, lecture )
        {
            if(lecture['id'] != current_lecture_id){
            __lectureObjects[lecture['id']] = lecture;
            __lectureOptionHtml += '<option value="'+lecture['id']+'">'+lecture['cl_lecture_name']+'('+__lectueTypeObjects[lecture['cl_lecture_type']]+')</option>';
            }
        });
    var __actionListItems   = '.lecture-action';
    var __lectureListItems  = '.lecture-dropdown';
    var __selectedTime      = '';

    $( "#from_time" ).focus(function() {
        
        var selectedDate        = $('#date_avail_from').val();
        var sel_date            = new Date(selectedDate);
        var today_date          = new Date();
        var today_date_second   = new Date(today_date.getFullYear(),today_date.getMonth(),today_date.getDate(),0,0,0,0);

        if(sel_date.getDate() == today_date_second.getDate()){

            var current_time = today_date.getHours();
            // $('#start_time').remove();
            // $('#live_lecture_start_time').prepend('<input type="text" class="form-control" id="start_time" placeholder="10.45" >');
            $('#from_time').timepicker({
                timeFormat: 'h:i A',
                interval: 60,
                minTime: (current_time+1).toString(),
                maxTime: '11:30pm',
                dynamic: false,
                dropdown: true,
                
            }).on('selectTime', function() {
                        
                        var selTime         = $(this).val();
                        __selectedTime      = selTime;
                        var current_time    = today_date.getHours();
                        $('#to_time').remove();
                        $('#to-time-container').prepend('<input id="to_time" class="form-control  text-center" type="text" name="restriction[available_till][time]" placeholder="Time" onkeydown="event.preventDefault()">');

                        var new_time = __selectedTime.split(":");
                        var setTime  = parseInt(new_time[0])+1+':'+new_time[1];
                        //console.log(setTime);
                        $('#to_time').timepicker({
                            timeFormat: 'h:i A',
                            interval: 60,
                            minTime: setTime.toString(),
                            maxTime: '11:30pm',
                            dynamic: false,
                            dropdown: true,
                            
                        });
                    });
        }
    });
    $( "#to_time" ).focus(function() {
        
        var selectedDate        = $('#date_avail_to').val();
        var sel_date            = new Date(selectedDate);
        var today_date          = new Date();
        var today_date_second   = new Date(today_date.getFullYear(),today_date.getMonth(),today_date.getDate(),0,0,0,0);

        if(sel_date.getDate() == today_date_second.getDate()){

            var current_time = today_date.getHours();
            // $('#start_time').remove();
            // $('#live_lecture_start_time').prepend('<input type="text" class="form-control" id="start_time" placeholder="10.45" >');
            var selectTime  = $( "#from_time" ).val();
            // var setNewTime  = __selectedTime!=''?__selectedTime:current_time;
            var new_time    = selectTime.split(":");
            var setTime     = parseInt(new_time[0])+1+':'+new_time[1];
            

            $('#to_time').timepicker({
                timeFormat: 'h:i A',
                interval: 60,
                minTime: selectTime==''?current_time.toString():setTime.toString(),
                maxTime: '11:30pm',
                dynamic: false,
                dropdown: true,
                
            });
            // $('#to_time').timepicker({
            //     timeFormat: 'h:i A',
            //     interval: 60,
            //     minTime: (current_time+1).toString(),
            //     maxTime: '11:30pm',
            //     dynamic: false,
            //     dropdown: true,
                
            // })
        }
    });
    function twelveto24(time){

        var hours = Number(time.match(/^(\d+)/)[1]);
        var minutes = Number(time.match(/:(\d+)/)[1]);
        var AMPM = time.match(/\s(.*)$/)[1];
        if(AMPM == "PM" && hours<12) hours = hours+12;
        if(AMPM == "AM" && hours==12) hours = hours-12;
        var sHours = hours.toString();
        var sMinutes = minutes.toString();
        if(hours<10) sHours = "0" + sHours;
        if(minutes<10) sMinutes = "0" + sMinutes;
        return sHours + ":" + sMinutes;
    }
    $(document).ready(function(){

        $("input[name='restriction[course_percentage][active]']").change(function(){
            if($(this).prop('checked') == true) {
                $("input[name='restriction[course_percentage][percentage]']").removeAttr('disabled');
            } else {
                $("input[name='restriction[course_percentage][percentage]']").attr('disabled', 'disabled');
                $("input[name='restriction[course_percentage][percentage]']").val('');
            }
        });

        $("input[name='restriction[available_from][active]']").change(function(){
            if($(this).prop('checked') == true) {
                $("input[name='restriction[available_from][date]'], input[name='restriction[available_from][time]']").removeAttr('disabled').addClass('allow_edit');
            } else {
                $("input[name='restriction[available_from][date]'], input[name='restriction[available_from][time]']").attr('disabled', 'disabled').removeClass('allow_edit');
                $('#date_avail_from').val('');
                $('#from_time').val('');
            }
        });

        $("input[name='restriction[available_till][active]']").change(function(){
            if($(this).prop('checked') == true) {
                $("input[name='restriction[available_till][date]'], input[name='restriction[available_till][time]']").removeAttr('disabled').addClass('allow_edit');
            } else {
                $("input[name='restriction[available_till][date]'], input[name='restriction[available_till][time]']").attr('disabled', 'disabled').removeClass('allow_edit');
                $('#date_avail_to').val('');
                $('#to_time').val('');
            }
        });
        // $("input[name='restriction[available_from][date]'], input[name='restriction[available_till][date]']").datepicker({
        //     language: 'en',
        //     minDate: new Date(),
        //     dateFormat: 'dd-mm-yy',
        //     autoClose: true
        // });

        $("input[name='restriction[available_from][date]']").datepicker({
            language: 'en',
            minDate: new Date(),
            // dateFormat: 'dd-mm-yy',
            onSelect: function(dateText, inst) {
               
                var sel_date            = new Date(dateText);
                var today_date          = new Date();
                var today_date_second   = new Date(today_date.getFullYear(),today_date.getMonth(),today_date.getDate(),0,0,0,0);

                if(sel_date.getDate() == today_date_second.getDate()){

                    var current_time = today_date.getHours();
                    $('#from_time').remove();
                    $('#from-time-container').prepend('<input id="from_time" class="form-control text-center" type="text" name="restriction[available_from][time]" placeholder="Time" onkeydown="event.preventDefault()">');
                    $('#from_time').timepicker({
                        timeFormat: 'h:i A',
                        interval: 60,
                        minTime: (current_time+1).toString(),
                        maxTime: '11:30pm',
                        dynamic: false,
                        dropdown: true,
                        
                    }).on('selectTime', function() {
                        
                        var selTime         = $(this).val();
                        __selectedTime      = selTime;
                        var current_time    = today_date.getHours();
                        $('#to_time').remove();
                        $('#to-time-container').prepend('<input id="to_time" class="form-control  text-center" type="text" name="restriction[available_till][time]" placeholder="Time" onkeydown="event.preventDefault()">');

                        var newTime     = twelveto24(__selectedTime);
                        var new_time    = newTime.split(":");
                        var setTime     = parseInt(new_time[0])+1+':'+new_time[1];
                        $('#to_time').timepicker({
                            timeFormat: 'h:i A',
                            interval: 60,
                            minTime: setTime.toString(),
                            maxTime: '11:30pm',
                            dynamic: false,
                            dropdown: true,
                            
                        });
                    });
                }else{

                    $('#from_time').remove();
                    $('#from-time-container').prepend('<input id="from_time" class="form-control text-center" type="text" name="restriction[available_from][time]" placeholder="Time" onkeydown="event.preventDefault()">');
                    $('#from_time').timepicker({
                        timeFormat: 'h:i A',
                        interval: 60,
                        maxTime: '11:30pm',
                        dynamic: false,
                        dropdown: true,
                    })
                }

                var selectedDate = new Date(dateText);
                // var endDate = new Date(selectedDate);
                $("input[name='restriction[available_till][date]").datepicker("option", "minDate", selectedDate);

            }
            
        });
        $("input[name='restriction[available_till][date]").datepicker({
            
            language: 'en',
            minDate: new Date(),
            // dateFormat: 'dd-mm-yy',
            onSelect: function(dateText, inst) {
                var sel_date            = new Date(dateText);
                var today_date          = new Date();
                var today_date_second   = new Date(today_date.getFullYear(),today_date.getMonth(),today_date.getDate(),0,0,0,0);
                
                if(sel_date.getDate() == today_date_second.getDate()){

                    var current_time = today_date.getHours();
                    $('#to_time').remove();
                    $('#to-time-container').prepend('<input id="to_time" class="form-control  text-center" type="text" name="restriction[available_till][time]" placeholder="Time" onkeydown="event.preventDefault()">');

                    var selectTime  = $( "#from_time" ).val();
                    
                    var setNewTime  = __selectedTime!=''?__selectedTime:selectTime;
                    var new_time    = setNewTime.split(":");
                    var setTime     = parseInt(new_time[0])+1+':'+new_time[1];
                    
                    $('#to_time').timepicker({
                        timeFormat: 'h:i A',
                        interval: 60,
                        minTime: setTime.toString(),
                        maxTime: '11:30pm',
                        dynamic: false,
                        dropdown: true,
                        
                    });
                }else{

                    $('#to_time').remove();
                    $('#to-time-container').prepend('<input id="to_time" class="form-control  text-center" type="text" name="restriction[available_till][time]" placeholder="Time" onkeydown="event.preventDefault()">');
                    $('#to_time').timepicker({
                        timeFormat: 'h:i A',
                        interval: 60,
                        maxTime: '11:30pm',
                        dynamic: false,
                        dropdown: true,
                    });
                }  
            }
        });

        $("input[name='restriction[available_from][time]'], input[name='restriction[available_till][time]']").timepicker({timeFormat: 'h:i A'});

        if(Object.keys(__lectureObjects).length > 0 ) {
            if($(__lectureListItems).length > 0 ) {
                $(__lectureListItems).html(__lectureOptionHtml)
                $(__lectureListItems).each(function(index, item) {
                    var lectureId   = $(item).attr('data-lecture-id');
                    var lecture     = __lectureObjects[lectureId];
                    var rowId       = $(item).parents('.activities-row').attr('data-id');
                    $("select[name='restriction[activities]["+rowId+"][action]']").html(getLectureActionHtml((lecture['cl_lecture_type'] == 3 || lecture['cl_lecture_type'] == 8)?1:0));
                    $(item).val(lectureId);
                });
            }
        }

        if($(__actionListItems).length > 0 ) {
            $(__actionListItems).each(function(index, item) {
                $(item).val($(item).attr('data-action-id'));
            });
        }

        $("input[type='radio'][name='cl_limited']").change(function(){
            if( $("input[type='radio'][name='cl_limited']:checked").val() == 1 ) {
                $("input[name='cl_limited_access']").removeAttr('disabled');
            } else {
                $("input[name='cl_limited_access']").val('');
                $("input[name='cl_limited_access']").attr('disabled', 'disabled');
            }
        });
    }); 
    function toUnixStamp(str)
    {
        var s=str.split("/");
        if(s.length>1)return (new Date(Date.UTC(s[2],s[0],s[1],0,0,0)).getTime()/1000.0);
    }
    function saveAccessRestriction() {
        var errorCount      = 0;
        var errorMessage    = [];
        
        if($("input[type='radio'][name='cl_limited']:checked").val() == 1) {
            var accessLimit = $.trim($("input[name='cl_limited_access']").val());
            if(isNaN(accessLimit) == false ) {
                if( accessLimit <= 0 ) {
                    errorCount++;
                    errorMessage.push('Access limit must be greater than Zero');
                }
            } else {
                errorCount++;
                errorMessage.push('Access must be an number');
            }
        }
        if( $("input[type='checkbox'][name='restriction[course_percentage][active]']").prop('checked') == true ) {
            var completionPercentage = $.trim($("input[name='restriction[course_percentage][percentage]']").val());
            if(isNaN(completionPercentage) == false ) {
                if( completionPercentage <= 0 || completionPercentage > 100 ) {
                    errorCount++;
                    errorMessage.push('Percentage of Course Completion must be in between 1 and 100');
                }
            } else {
                errorCount++;
                errorMessage.push('Invalid Course completion percentage');
            }
        }

        if( $("input[type='checkbox'][name='restriction[available_from][active]']").prop('checked') == true ) {
            var availableFromDate = $.trim($("input[name='restriction[available_from][date]']").val());
            var availableFromTime = $.trim($("input[name='restriction[available_from][time]']").val());
            if( availableFromDate == '' ) {
                errorCount++;
                errorMessage.push('Available From date must not be empty');
            }
            if(availableFromTime == ''){
                errorCount++;
                errorMessage.push('Available From time must not be empty');
            }
        }

        if( $("input[type='checkbox'][name='restriction[available_till][active]']").prop('checked') == true ) {
            var availableTillDate = $.trim($("input[name='restriction[available_till][date]']").val());
            var availableTillTime = $.trim($("input[name='restriction[available_till][time]']").val());
            if( availableTillDate == '' ) {
                errorCount++;
                errorMessage.push('Available Till date must not be empty');
            }
            if( availableTillTime == '' ) {
                errorCount++;
                errorMessage.push('Available Till time must not be empty');
            }
            if($("input[type='checkbox'][name='restriction[available_from][active]']").prop('checked') == false){

                errorCount++;
                errorMessage.push('Available From date must not be empty');
            }
            
        }
        if(availableFromDate!=undefined){
            var fromDate    = toUnixStamp(availableFromDate);
        }
        if(availableTillDate!=undefined){
            var toDate      = toUnixStamp(availableTillDate);
        }
        
        if(fromDate > toDate){
            errorCount++;
            errorMessage.push('Available Till date must be greater than Available From date');
        }
        if(availableFromDate != undefined && availableTillDate != undefined && availableFromDate != '' && availableFromDate == availableTillDate){

            var ftime = twelveto24(availableFromTime);
            var ttime = twelveto24(availableTillTime);
            if(ftime >= ttime){
                errorCount++;
                errorMessage.push('Available Till time must be greater than Available From time');
            }
        }

        if($(__actionListItems).length > 0 ) {
            $(__actionListItems).each(function(index, item) {
                var rowId   = $(item).parents('.activities-row').attr('data-id');
                var lecture = $.trim($("select[name='restriction[activities]["+rowId+"][lecture_id]']").val());
                
                if(lecture == '') {
                    errorCount++;
                    errorMessage.push('Choose lecture for <b>Rule '+(rowId)+'</b>');
                }
                if($(item).val() == 'complete_with_percentage') {
                    var completionPercentage = $.trim($("input[name='restriction[activities]["+rowId+"][percentage]']").val());
                    if(isNaN(completionPercentage) == false ) {
                        if( completionPercentage <= 0 || completionPercentage > 100 ) {
                            errorCount++;
                            errorMessage.push('Completion percentage must be in between 1 and 100 for <b>Rule '+(index+1)+'</b>');
                        }
                    } else {
                            errorCount++;
                            errorMessage.push('Invalid Completion percentage found at <b>Rule '+(index+1)+'</b>');
                    }
                }
            });
        }

        if(errorCount > 0){
            var messageObject = {
                'body':errorMessage.join('<br />'),
                'button_yes':'OK', 
                };
                
            callback_warning_modal(messageObject, removeActivityConfirmed);
        } else {
            $('#access_restriction_form').submit();
        }
    }

    var renderingActivity = false;
    function generateActivity() {
        if(renderingActivity == true) {
            return false;
        }//__activityCount
        renderingActivity = true;
        var activityHtml = '';
            activityHtml += '<div class="row activities-row" data-id="'+__activityCount+'" id="activity_row_'+__activityCount+'">';
            activityHtml += '   <label class="activity-count-label">Rule '+__activityCount+'</label>';
            activityHtml += '   <div class="clearfix"></div>';
            activityHtml += '   <div class="col-sm-5 lecture-dropdown-wrapper">';
            activityHtml += '       <select class="form-control lecture-dropdown" name="restriction[activities]['+__activityCount+'][lecture_id]">'+__lectureOptionHtml+'</select>';
            activityHtml += '   </div>';
            activityHtml += '   <div class="col-sm-5">';
            activityHtml += '        <select class="form-control lecture-action" name="restriction[activities]['+__activityCount+'][action]">'+getLectureActionHtml(0)+'</select>';
            activityHtml += '   </div>';
            activityHtml += '   <div class="col-sm-1 no-padding">';
            activityHtml += '         <input disabled="disabled" class="form-control text-center" maxlength="3" value="" type="text" name="restriction[activities]['+__activityCount+'][percentage]">';
            activityHtml += '   </div>';
            activityHtml += '   <div class="col-sm-1">';
            activityHtml += '       <span onclick="removeActivity(\''+__activityCount+'\', \''+__activityCount+'\')" class="close-activity-btn">×</span>';
            activityHtml += '   </div>';
            activityHtml += '</div>';
            __activityCount++;
            $('#activities_wrapper').append(activityHtml);
            renderingActivity = false;
    }

    function removeActivity(activityId, activityRow) {
        // var messageObject = {
        //     'body':'Are you sure to remove <b>rule '+activityRow+'</b>',
        //     'button_yes':'REMOVE', 
        //     'button_no':'CANCEL',
        //     'continue_params':{'activityId':activityId},
        // };
        // callback_warning_modal(messageObject, removeActivityConfirmed);
        var param = {};
            param['data'] = {'activityId':activityId};
            removeActivityConfirmed(param);
    }

    function removeActivityConfirmed(param) {
        $('#activity_row_'+param.data.activityId).remove();
        $('#common_message_advanced').modal('hide');
        var activities = $('.activities-row');
        if(activities.length < 1){
            __activityCount = 1;
        }
    }

    $(document).on( 'change', '.lecture-action', function(){
        var activityId = $(this).parents('.activities-row').attr('data-id');
        if($(this).val() == 'complete_with_percentage') {
            $("input[name='restriction[activities]["+activityId+"][percentage]']").removeAttr('disabled');
        } else {
           
            $("input[name='restriction[activities]["+activityId+"][percentage]']").attr('disabled', 'disabled');
        }
    });

    $(document).on( 'change', '.lecture-dropdown', function(){
        var activityId = $(this).parents('.activities-row').attr('data-id');
        var lectureId = $(this).val();
        var lectureObject = __lectureObjects[lectureId];
        if(lectureObject['cl_lecture_type'] == 3 || lectureObject['cl_lecture_type'] == 8) {
            $("select[name='restriction[activities]["+activityId+"][action]']").html(getLectureActionHtml(1));
        } else {
            $("input[name='restriction[activities]["+activityId+"][percentage]']").attr('disabled', 'disabled');
            $("input[name='restriction[activities]["+activityId+"][percentage]']").val('');
            $("select[name='restriction[activities]["+activityId+"][action]']").html(getLectureActionHtml(0));
        }
    });

    function getLectureActionHtml(type) {
        var actionHtml = '';
        switch(type) {
            case 1:
                actionHtml = '<option value="complete">Must Complete</option><option value="complete_with_percentage">Must Complete with %</option>';
            break;
            default:
                actionHtml = '<option value="complete">Must Complete</option>';
            break;
        }
        return actionHtml;
    }
    function validateForm(){

        var GivenDate = $('#avail_date_from').val();
        var GivenDateTo = $('#avail_date_to').val();

        if ($('#avail-from').is(':checked')) {
            
            if(GivenDate!=null||GivenDate==''||GivenDate=='undefined'){
               
                myDate=GivenDate.split("-");
                var newDate=myDate[0]+"-"+myDate[1]+"-"+myDate[2];
                var CurrentDate = new Date();
                GivenDate = parseInt((new Date(newDate).getTime() / 1000).toFixed(0));
                CurrentDate = parseInt((new Date(CurrentDate).getTime() / 1000).toFixed(0));
                if(GivenDate < CurrentDate){
                    errorMessage='Please select a valid available from date';
                    var messageObject = {
                            'body':errorMessage,
                            'button_yes':'OK', 
                    };
                    callback_warning_modal(messageObject);
                    return false;
                } 
            }
        }
        if ($('#avail-to').is(':checked')) {
            
            if(GivenDateTo!=null||GivenDateTo==''||GivenDateTo=='undefined'){
               
                myDate=GivenDateTo.split("-");
                var newDate=myDate[0]+"-"+myDate[1]+"-"+myDate[2];
                var CurrentDate = new Date();
                GivenDateTo = parseInt((new Date(newDate).getTime() / 1000).toFixed(0));
                CurrentDate = parseInt((new Date(CurrentDate).getTime() / 1000).toFixed(0));
                if(GivenDateTo < CurrentDate){
                    errorMessage='Please select a valid available till date';
                    var messageObject = {
                            'body':errorMessage,
                            'button_yes':'OK', 
                    };
                    callback_warning_modal(messageObject);
                    return false;
                } 
            }
        }
    }
</script>
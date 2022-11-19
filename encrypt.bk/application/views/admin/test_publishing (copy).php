<?php include_once 'coursebuilder/lecture_header.php';?>
<!-- ADDING REDACTOR PLUGIN INSIDE -->
<!-- ############################# --> <!-- START -->
    <link rel="stylesheet" href="<?php echo assets_url() ?>css/redactor/css/alignment.css" />

<!-- ############################# --> <!-- END -->
<link rel="stylesheet" href="<?php echo assets_url() ?>css/datepicker.min.css">
<link rel="stylesheet" href="<?php echo assets_url() ?>css/timepicker.css">
<link rel="stylesheet" href="<?php echo assets_url() ?>css/jquery.timepicker.min.css">
<link rel="stylesheet" href="<?php echo assets_url() ?>css/jquery-ui.css">

<link href="<?php echo assets_url() ?>css/bootstrap-tagsinput.css" rel="stylesheet" type="text/css" />
<style>
    .noborder{border: none !important;}
    .restriction-table td{
            padding: 5px;
        }
        .restrict-btn{
            margin: 30px 0;
        }
        #addrow{
            font-size: 26px;
            font-weight: 800;
            color: #09bf63;
            cursor: pointer;
            user-select:none;
        }
        #addrow:hover{
            color:#048544;
        }
        .inline-block{
            display: inline-block;
        }
        .access-container{
            background: #fff;
            border-radius: 3px;
            padding: 30px;
            margin: 10px;
        }
        .ui-timepicker-standard{
            border: none;
        }
        .rem-icn{
            width: 25px;
            height: 25px;
            cursor: pointer;
        }
        td.percentage{width: 75px;}
        .delrow{
            font-size: 26px;
            font-weight: 800;
            color: #f70000;
            cursor: pointer;
            user-select:none;
        }
        .addtest-checkbox input[type='checkbox'] {
            opacity: 0;
            left: 0px;
            margin-right: 0px;
            width: 18px;
            height: 18px;
            top: 0px;
            z-index: 999;
            cursor: pointer;
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
    <div class="list-group test-listings">
      <a href="javascript:void(0)" class="list-group-item active">
        <span class="font15">Instructions</span>
      </a>
      <a href="javascript:void(0)" class="list-group-item link-style">
          <span class="green-span"><i class="icon icon-ok-circled"></i></span>          
          <span class="listing-text">Once the quiz is published, then only the student can access it.</span>
      </a>
      <a href="javascript:void(0)" class="list-group-item link-style">
          <span class="green-span"><i class="icon icon-ok-circled"></i></span>          
          <span class="listing-text">The quiz can be accessible by the students only between the scheduled date and time.</span>          
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

            <div class="row-fluid course-cont">
                <div class="col-sm-12">
                    <div class="form-horizontal" id="course_form">
                        <?php // include_once('messages.php') ?>
                        <form class="form-horizontal" id="save_test_basics" method="post" action="<?php echo admin_url('test_manager/test_publishing'.'/'.base64_encode($test['id'])); ?>">
                            
                            <div class="each-steps" id="step-four">
                                
                                
            <div class="form-group">               
                <div class="row">
                    <div class="col-sm-12 arrangement-grouping">
                        <div class="arrangement-panel">Access Restriction Settings</div>
                        <div class="addtest-container">
                            <div class="addtest-checkbox" style="border-bottom:solid 1px #ccc;padding-bottom:15px;">
                              
                                <div class="access-container">
                
                                        <table class="restriction-table table-responsive">
                                            <tbody>
                                                <tr>
                                                    <td><span>Student must match </span></td>
                                                    <td>
                                                        <select class="form-control" name="rule_option">
                                                            <option value="1" <?php if($test['rule_availability']==1){ echo 'selected'; } ?>>All</option>
                                                            <option value="2" <?php if($test['rule_availability']==2){ echo 'selected'; } ?>>Any</option>
                                                        </select>
                                                    </td>
                                                    <td><span>&nbsp; of the following rules to access this quiz</span></td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <input type="checkbox" style="opacity:1;" value="1" class="form-control inline-block" name="from_check" id="from_check" <?php if($test['a_from_availability']==1){ echo 'checked'; } ?>>
                                                        <span>Available From</span>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="from_date" placeholder="dd-mm-yyyy" class="form-control" id="from-date" autocomplete="off" value="<?php if($test['a_from']!=NULL){ echo date("d-m-Y", strtotime($test['a_from'])); } ?>" <?php if($test['a_from_availability']!=1){ echo 'disabled'; } ?> readonly="" style="background: #fff;">
                                                    </td>
                                                    <td>
                                                        <input type="text" name="from_time" onkeydown="event.preventDefault()" placeholder="From Time" class="form-control timepicker timepicker-with-dropdown" id="from-time" autocomplete="off" value="<?php echo $test['a_from_time']; ?>" <?php if($test['a_from_availability']!=1){ echo 'disabled'; } ?>>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <input type="checkbox" style="opacity:1;" value="1" class="form-control inline-block" name="to_check" id="to_check" <?php if($test['a_to_availability']==1){ echo 'checked'; } ?>>
                                                        <span>Available To</span>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="to_date" placeholder="dd-mm-yyyy" class="form-control" id="to-date" autocomplete="off" value="<?php if($test['a_to']!=NULL){ echo date("d-m-Y", strtotime($test['a_to'])); } ?>" <?php if($test['a_to_availability']!=1){ echo 'disabled'; } ?> readonly="" style="background: #fff;">
                                                    </td>
                                                    <td>
                                                        <input type="text" name="to_time" placeholder="To Time" onkeydown="event.preventDefault()" class="form-control timepicker timepicker-with-dropdown" id="to-time" autocomplete="off" value="<?php echo $test['a_to_time']; ?>" <?php if($test['a_to_availability']!=1){ echo 'disabled'; } ?>>
                                                    </td>
                                                </tr>
                                                
                                            <tbody>
                                        </table>
                                        
                                        <table class="restriction-table order-list" id="activity-table">
                                            <tbody>
                                                <?php 
                                             
                                                $acitivity_count = (isset($available_rules))?count($available_rules):'1';
                                                if(!empty($available_rules)){
                                                $rule = 1;
                                                foreach($available_rules as $available_rule){ ?>
                                                <!-- activity row -->
                                                <tr data-geomet="<?php echo $available_rule['id']; ?>" id="del_row_<?php echo $available_rule['id']; ?>">
                                                    <td width="143"><span>Rule Completion</span></td>
                                                    <td>
                                                        <select class="form-control" onchange="changeActivity('<?php echo $rule; ?>');" name="activity_selection[]" id="sel_activity_<?php echo $rule; ?>">
                                                        <option value="0" data-id="0">Choose activity</option>
                                                            <?php foreach($sections as $section){ ?>
                                                                <option value="<?php echo $section['id']; ?>" data-id="<?php echo $section['type']; ?>" <?php if($available_rule['selected_lecture']==$section['id']){ echo 'selected'; } ?>><?php echo $section['key'].' - '.$section['lecture'].'('.$lecture_types[$section['type']].')'; ?></option>
                                                            <?php } ?>
                                                         </select>
                                                    </td>
                                                    <td>
                                                        <select class="form-control" id="activity_option_<?php echo $rule; ?>" name="activity_option[]" onchange="showPercentage('<?php echo $rule; ?>')">
                                                        <option value="1" data-id="1" <?php if($available_rule['activity_option']==1){ echo 'selected'; } ?>>Must be complete </option>
                                                        <?php if($section['type']!=1){ ?>
                                                        <option value="2" data-id="2" <?php if($available_rule['activity_option']==2){ echo 'selected'; } ?>>Must be complete with percentage &ge;</option>
                                                        <?php } ?>
                                                        </select>
                                                    </td>
                                                        <td class="percentage" id="percentage_<?php echo $rule; ?>" <?php if($available_rule['activity_option']==1){ ?> style="display:none;" <?php } ?>>
                                                        <input type="number" min="0"  onkeypress="return isNumber(event)" id="per_value_<?php echo $rule; ?>" name="percentage[]" class="form-control percentage" value="<?php echo $available_rule['percentage']; ?>" placeholder="%">
                                                        <input type="hidden"  id="rule_<?php echo $rule; ?>" name="rule_id[]" class="form-control" value="<?php echo ($available_rule['id'])?$available_rule['id']:'0'; ?>">
                                                    </td>
                                                    <?php if($rule==1){ ?>
                                                        <td><span id="addrow">+</span></td>
                                                    <?php }  else { ?>
                                                        <td><span class="delrow" >×</span></td>
                                                    <?php } ?>
                                                </tr>
                                                <!-- activity row ends -->
                                                <?php
                                                $rule++;
                                                }
                                                } else{
                                                ?>
                                                <tr>
                                                    <td width="143"><span>Rule Completion</span></td>
                                                    <td>
                                                        <select class="form-control" onchange="changeActivity(1);" name="activity_selection[]" id="sel_activity_1">
                                                            <option value="0" data-id="0">Choose activity</option>
                                                            <?php foreach($sections as $section){ ?>
                                                                <option value="<?php echo $section['id']; ?>" data-id="<?php echo $section['type']; ?>"><?php echo $section['key'].' - '.$section['lecture'].'('.$lecture_types[$section['type']].')'; ?></option>
                                                            <?php } ?>
                                                         </select>
                                                    </td>
                                                    <td>
                                                        <select class="form-control" id="activity_option_1" name="activity_option[]" onchange="showPercentage(1)">
                                                        <option value="1" data-id="1">Must be complete </option>
                                                        </select>
                                                    </td>
                                                    <td class="percentage" id="percentage_1" style="display:none;">
                                                        <input type="number" min="0"  onkeypress="return isNumber(event)" id="per_value_1" name="percentage[]" class="form-control" value="" placeholder="%">
                                                        <input type="hidden"  id="rule_1" name="rule_id[]" class="form-control" value="0">
                                                    </td>
                                                    <td><span id="addrow">+</span></td>
                                                </tr>
                                                <?php
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                </div>
                                <!-- acces permission ends -->
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
<script type="text/javascript" src="<?php echo assets_url() ?>js/jquery-ui.min.js"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/jquery.timepicker.min.js"></script>
  
<script type="text/javascript">
    var __assessment_id    = '<?php echo $test['id']; ?>';
</script>
<!-- JS -->

<script type="text/javascript">
    var __activity_count = '<?php echo ($acitivity_count!=0)?$acitivity_count:1; ?>';
    function publishTest(This){
        $.ajax({
            url: webConfigs('admin_url')+'test_manager/ajax_publish',
            type: "POST",
            data:{ "is_ajax":true,"assessment_id":btoa(__assessment_id)},
            success: function(response){
                var data  = $.parseJSON(response);
                if(data['success'] == true){
                    if($('#test_publish').is(':checked')){
                        $(This).prop('checked',true);
                    }else{
                        $(This).prop('checked',false);
                    }
                }else{
                    if($('#test_publish').is(':checked')){
                        $(This).prop('checked',false);
                    }else{
                        $(This).prop('checked',true);
                    }
                    lauch_common_message(data['message']);
                }
            }
        });
    }

    function saveNext(){
        $('#savenextform').val('1');
        $('#submittedform').val('1');
        if(validateStep('step-four')){
            $('#save_test_basics').submit();
        }
    }

    function save(){
        $('#savenextform').val('0');
        $('#submittedform').val('1');
        if(validateStep('step-four')){
            $('#save_test_basics').submit();
        }
    }

    function toDate(dateStr) {
        var parts = dateStr.split("-")
        return new Date(parts[2], parts[1] - 1, parts[0])
    }


    function validateStep(step){
        switch(step){
            case 'step-four':
                var from_date   = $("#from-date").val();
                var to_date     = $("#to-date").val();
                var from_time   = $("#from-time").val();
                var to_time     = $("#to-time").val();

                for(i=0; i<=__activity_count; i++){
                    if(($("#per_value_"+__activity_count).val())==''){
                        $("#per_value_"+__activity_count).val('0');
                    }
                }

                if($('#from_check').is(":checked")){
                    if((from_date=='') || (from_time=='')){
                        var messageObject = {
                            'body':'Please enter the valid from date and time.',
                            'button_yes':'OK', 
                            'button_no':'CANCEL'
                        };
                        callback_warning_modal(messageObject);
                        return false;
                    }
                }

                if($('#to_check').is(":checked")){
                    if((to_date=='') || (to_time=='')){
                        var messageObject = {
                            'body':'Please enter the valid to date and time.',
                            'button_yes':'OK', 
                            'button_no':'CANCEL'
                        };
                        callback_warning_modal(messageObject);
                        return false;
                    }
                }

                // if(!$('#from_check').is(":checked")){
                //     $("#textbox1").attr("disabled", "disabled");
                //     $("#from-date").removeAttr("disabled");
                //     $("#to-date").removeAttr("disabled"); 
                //     $("#from-date").val(""); 
                //     $("#from-time").val(""); 
                // }

                // if(!$('#to_check').is(":checked")){
                //     $("#textbox1").attr("disabled", "disabled");
                //     $("#from-date").removeAttr("disabled");
                //     $("#to-date").removeAttr("disabled"); 
                //     $("#to-date").val(""); 
                //     $("#to-time").val(""); 
                // }

                if($('#from_check').is(":checked") && ($('#to_check').is(":checked"))){
                    from_date   = toDate(from_date);
                    to_date     = toDate(to_date);
                    from_date.setHours(0,0,0,0);
                    to_date.setHours(0,0,0,0);
                    if(from_date > to_date){
                        var messageObject = {
                            'body':'Available to date is should be greater than from date.',
                            'button_yes':'OK', 
                            'button_no':'CANCEL'
                        };
                        callback_warning_modal(messageObject);
                        return false;
                    }
                    
               
                    //if((from_date=to_date)){
                    if (from_date-to_date === 0){
                        var aDate = convertHoursformat(from_time);
                        var bDate = convertHoursformat(to_time);
                        if(aDate >= bDate){
                            var messageObject = {
                            'body':'End time should be greater than start time.',
                            'button_yes':'OK', 
                            'button_no':'CANCEL'
                             };
                            callback_warning_modal(messageObject);
                            return false;
                        }
                    }
                }
               
            break;
        }

        return true;
    }

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
    $('.percentage').bind("cut copy paste",function(e) {
     e.preventDefault();
    });
    // acess permission add row
    $(document).ready(function () {
            $("#from_check").click(function(){
                if($('#from_check').is(":checked")){
                    $("#from-date").removeAttr("disabled"); 
                    $("#from-time").removeAttr("disabled"); 
                } else {
                    $("#from-date").val(""); 
                    $("#from-time").val(""); 
                    $("#from-date").attr("disabled", "disabled");
                    $("#from-time").attr("disabled", "disabled");
                }
            });
            $("#to_check").click(function(){
                if($('#to_check').is(":checked")){
                    $("#to-date").removeAttr("disabled"); 
                    $("#to-time").removeAttr("disabled"); 
                } else {
                    $("#to-date").val(""); 
                    $("#to-time").val(""); 
                    $("#to-date").attr("disabled", "disabled");
                    $("#to-time").attr("disabled", "disabled");
                }
            });
            var __counter = 0;
            $("#addrow").on("click", function () {
                __activity_count = parseInt(__activity_count)+1;
                var newRow = $("<tr>");
                var cols = "";
                cols += '<td width="143"><span>Rule Completion</span></td>';
                cols += '<td width="">';
                cols += '<select class="form-control" onchange="changeActivity('+__activity_count+');" name="activity_selection[]" id="sel_activity_'+__activity_count+'">';
                cols += '<option value="0" data-id="0">Choose activity</option>';
                <?php
                foreach($sections as $section){ 
                ?>
                cols += '<option value="<?php echo $section['id']; ?>" data-id="<?php echo $section['type']; ?>"><?php echo $section['key'].' - '.$section['lecture'].'('.$lecture_types[$section['type']].')'; ?></option>';
                <?php
                }
                ?>
                cols += '</select>';
                cols += '</td>';
                cols += '<td width=""><select class="form-control" id="activity_option_'+__activity_count+'" name="activity_option[]" onchange="showPercentage('+__activity_count+')"><option value="1" data-id="1">Must be complete </option></select></td>';
                cols += '<td width="75" id="percentage_'+__activity_count+'" style="display:none;"><input type="number" min="0"  onkeypress="return isNumber(event)" id="per_value_'+__activity_count+'" name="percentage[]" class="form-control"  placeholder="%"><input type="hidden"  id="rule_'+__activity_count+'" name="rule_id[]" class="form-control" value="0"></td>';
                cols += '<td><span class="delrow">&times;<span></td>';
                newRow.append(cols);
                $("table.order-list").append(newRow);
                __counter++;
            });

            $("table.order-list").on("click", ".delrow", function (event) {
                var $row = jQuery(this).closest("tr");
                var id = $row.data("geomet");
                if(id!=undefined){
                    var messageObject = {
                    'body':'Are you sure to delete this rule?',
                    'button_yes':'CONTINUE', 
                    'button_no':'CANCEL',
                    'continue_params':{'id':id}
                    };
                    callback_warning_modal(messageObject, deleteRule);
                } else {
                    $(this).closest("tr").remove();       
                    __counter -= 1
                }
            });
        });
        $(function(){
        var today = new Date();
        $("#from-date,#to-date").datepicker({
            language: 'en',
            minDate: today,
            dateFormat: 'dd-mm-yy',
            autoClose: true
        });
        $('input.timepicker').timepicker({});
        });
        function changeActivity(id){
            var data_id=$("#sel_activity_"+id).find(':selected').attr('data-id');
            $('#activity_option_'+id).html('');
            $('#per_value_'+id).val('');
            $('#percentage_'+id).hide();
            var activityHtml ='';
            if(data_id==3){
            activityHtml +='<option value="1" data-id="1">Must be complete </option>';
            } else {
                activityHtml +='<option value="1" data-id="1">Must be complete </option>';
                activityHtml +='<option value="2" data-id="2">Must be complete with percentage &ge;</option>';
            }
            $('#activity_option_'+id).html(activityHtml);
        }
        function showPercentage(id){
            var data_id=$("#activity_option_"+id).find(':selected').attr('data-id');
            if(data_id==1){
                $('#percentage_'+id).hide();
            } else {
                $('#percentage_'+id).show();
            }
            $('#activity_option_'+id).html(activityHtml);
        }
        function deleteRule(params){
            if(params.data.id!=undefined){
                $("#del_row_"+params.data.id).remove();   
                $("#advanced_confirm_box_cancel").click();    
            }
        }
        function isNumber(evt) {
            evt = (evt) ? evt : window.event;
            var charCode = (evt.which) ? evt.which : evt.keyCode;
            if (charCode > 31 && (charCode < 48 || charCode > 57)) {
                return false;
            }
            return true;
        }
</script>

<?php include_once 'footer.php';?>
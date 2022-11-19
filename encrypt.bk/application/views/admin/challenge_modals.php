<style>
    .upload-file-name {
        background: rgb(255, 255, 255) none repeat scroll 0 0;
        border: 0 none;
        cursor: pointer;
        height: 100%;
        left: 0;
        margin: 0;
        padding: 8px 12px;
        position: absolute;
        width: calc(100% - 124px);
        z-index: 0;
    }
</style>
<!-- Modal pop up contents :: Create Live Lecture -->
    <div class="modal fade" data-backdrop="static" data-keyboard="false" id="challenge_zone" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-small" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon icon-cancel-1"></span></button>
                    <h4 class="modal-title" id="myModalLabel"><?php echo lang('create_new_challenge') ?></h4>
                </div>
                <div class="modal-body">
                    <div class="form-group challenge_zone_form">
                        
                    </div>
                    <div class="form-group">
                        <label><?php echo lang('challenge_title') ?> *:</label>
                        <input type="text" maxlength="80" placeholder="<?php echo lang('challenge_title_ph') ?>" class="form-control" id="challenge_name">
                    </div>
                    
                    
                    
                    <!-- Click to get add the cateogry to the select box -->
                     <div class="form-group clearfix">
                        <div class="row">
                            <div class="add-selectn col-sm-9 alignment-order">
                                <label><?php echo lang('category') ?> *:</label>
                                <select class="form-control" id="challenge_zone_category">
                                    <option value="0"><?php echo lang('select_category') ?></option>
                                    <?php foreach($categories as $category): ?>
                                        <option value="<?php echo $category['id'] ?>"><?php echo $category['ct_name'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-sm-3">
                                <label><?php echo lang('duration') ?> *:</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" maxlength="3" id="challenge_duration" onkeypress="return preventAlphabets(event)" placeholder="<?php echo lang('duration_ph') ?>">
                                    <div class="input-group-addon"><?php echo lang('duration_min') ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group clearfix">
                        <div class="row">
                            <div class="col-sm-3">
                                <label><?php echo lang('start_date') ?> *:</label>
                                <input type="text" autocomplete="off" placeholder="" id="challenge_pop_start_date" class="form-control" >
                            </div>
                            <div class="col-sm-3">
                                <label><?php echo lang('start_time') ?> *:</label>
                                <div class="input-group" id="challenge_div_start_time">
                                    <input type="text" class="form-control" id="challenge_pop_start_time" placeholder="10.45">
                                    <div class="input-group-addon" id="challenge_pop_start_time_session">AM </div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <label><?php echo lang('end_date') ?> *:</label>
                                <input type="text" autocomplete="off" placeholder="" id="challenge_pop_end_date" class="form-control" >
                            </div>
                            <div class="col-sm-3">
                                <label><?php echo lang('end_time') ?> *:</label>
                                <div class="input-group" id="challenge_div_end_time">
                                    <input type="text" class="form-control" id="challenge_pop_end_time" placeholder="10.45">
                                    <div class="input-group-addon" id="challenge_pop_end_time_session">AM </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group clearfix">
                        <div class="checkbox"><label><input checked="checked" id="cz_show_categories" value="1" type="checkbox"><span class="ap_cont chk-box">Show Categories</span><br><small>(If you select this option, categories tab will display in assessment page)</small></label></div>
                    </div>
                   
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-green" onclick="createChallenge()"><?php echo lang('create') ?></button>
                    <button type="button" class="btn btn-red" id="cancel_challenge" data-dismiss="modal"><?php echo lang('cancel') ?></button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal pop up contents :: Live Lecture -->
    
    <!-- Modal pop up contents:: Delete Section popup-->
        <div class="modal fade alert-modal-new" id="challenge_delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog modal-small" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="form-group">
                            <b id="challenge_confirm_box_title"></b>
                            <p class="m0" id="challenge_confirm_box_content"> </p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-red" data-dismiss="modal"><?php echo lang('cancel') ?></button>
                        <button type="button" class="btn btn-green" id="challenge_confirm_box_ok"><?php echo lang('continue') ?></button>
                    </div>
                </div>
            </div>
        </div>
   <!-- !.Modal pop up contents :: Delete Section popup-->
   
   <!-- Modal pop up contents:: Challenge Section popup-->
        <div class="modal fade alert-modal-new" id="challenge_validation" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog modal-small" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="form-group">
                            <b id="challenge_validation_title"><?php echo lang('alert') ?></b>
                            <p class="m0" id="challenge_validation_box_content"><?php echo lang('check_any') ?></p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-green" data-dismiss="modal"><?php echo lang('ok') ?></button>
                    </div>
                </div>
            </div>
        </div>
   <!-- !.Modal pop up contents :: Challenge Section popup-->

<!-- Modal pop up contents:: Delete Section popup-->
    <div class="modal fade alert-modal-new" id="deleteSection" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-small" role="document">
            <div class="modal-content">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon icon-cancel-1"></span></button>
                <div class="modal-body">
                    <span><i class="icon icon-attention-alt"></i></span>
                    <div class="form-group">
                        <b id="delete_header_text"></b>
                        <p class="m0" id="delete_message"></p>
                        <p><?php echo lang('action_cannot_undone') ?></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-green" data-dismiss="modal"><?php echo lang('cancel') ?></button>
                    <button type="button" class="btn btn-red" id="delete_challenge_ok" ><?php echo strtoupper(lang('yes')) ?>, <?php echo strtoupper(lang('delete')) ?>!</button>
                </div>
            </div>
        </div>
    </div>
<!-- !.Modal pop up contents :: Delete Section popup-->

<!-- Modal pop up contents :: Upload a lecture -->
    <div class="modal fade padd-r20" data-backdrop="static" data-keyboard="false" id="addquestion" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-small" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon icon-cancel-1"></span></button>
                    <h4 class="modal-title" id="myModalLabel"><?php echo lang('upload_question') ?></h4>
                </div>
                <div class="modal-body">
                    <p class="mb30"><?php echo lang('follow_step_challenge') ?></p>
                    <div class="form-group mb30">
                        <p><b>Step 1:</b> Download the given Document <a href="<?php echo base_url('uploads/questiontemplate.docx') ?>" class="link-style"><em>template.doc</em></a> and analyze the format</p>
                    </div>  
                     <div class="form-group mb30">
                        <p><b>Step 2:</b> Fill your questions in the document format.</p>
                    </div>
                     <div class="form-group mb30">
                        <p><b>Step 3:</b> After you have filled with the questions, Upload your document.</p>
                    </div>
                    <div class="form-group clearfix">
                      <div class="fle-upload">
                        <label class="fle-lbl">BROWSE</label>
                        <input type="file" class="form-control upload" id="upload_question">
                        <input value="" readonly="" class="form-control upload-file-name" id="upload_challenge_file" type="text">
                      </div>
                    </div>
                    <div class="clearfix progress-custom" id="percentage_bar" style="display: none">
                        <div class="progress width100">
                            <div style="width: 60%;" aria-valuemax="100" aria-valuemin="0" aria-valuenow="60" role="progressbar" class="progress-bar progress-bar-success">
                                <span class="sr-only">60% Complete</span>
                            </div>
                        </div>
                        <span class="">Uploading...<b class="percentage-text">60%</b></span>
                    </div>

                    <div class="form-group mb30">
                        <p><b>Step 4:</b> Review your questions.</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-green" onclick="uploadQuestion()">UPLOAD</button>
                    <button type="button" class="btn btn-red" data-dismiss="modal">CANCEL</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal pop up contents :: Upload a lecture -->
<script type="text/javascript">


$(document).ready(function(){
    $('#challenge_pop_start_time').timepicker({ timeFormat: 'h:i A' });

    //$('#challenge_start_time').trigger('changeTime');
    
    $('#challenge_pop_end_time').timepicker({ timeFormat: 'h:i A' });

    //$('#challenge_end_time').trigger('changeTime');
    
    var today = new Date();
    var set_end_date = new Date();
    
    $("#challenge_pop_start_date").datepicker({
        language: 'en',
        minDate: today, // Now can select only dates, which goes after today
        onSelect: function(dateText, inst){

            catid = $("#challenge_zone_category").val();
            if(catid != ''){
                    $.ajax({
                    url: admin_url+'challenge_zone/check_start_date',
                    type: "POST",
                    data: {'startdate': dateText, catid: catid, challenge_id: challenge_id},
                    success: function(response){
                        $("#challenge_pop_end_date").val('');
                        data = $.parseJSON(response);
                        if(data['msg'] != ''){
                            $('#popUpMessage').html(data['msg']);
                        }else{
                            $('#popUpMessage').remove();
                        }
                        if(data['stat'] == 0){
                            cleanPopUpMessage();
                            errorMessage = data['msg'];
                            str          = renderPopUpMessage('error', errorMessage);
                            //$('.challenge_zone_form').prepend(renderPopUpMessage('error', errorMessage));
                            $('.challenge_zone_form').prepend(str);
                            scrollToTopOfPage();
                            $("#challenge_pop_start_date").val('');
                        }else{
                            
                            var sel_str_date = $('#challenge_pop_start_date').val();
            
                            var sel_date     = new Date(sel_str_date);
                            var today_date   = new Date();
                            var today_date_second = new Date(today_date.getFullYear(),today_date.getMonth(),today_date.getDate(),0,0,0,0);
                            if(sel_date.getTime() == today_date_second.getTime()){

                                $('#challenge_pop_start_time').remove();
                                $('#challenge_div_start_time').prepend('<input type="text" class="form-control" id="challenge_pop_start_time" placeholder="10.45" >');
                                $('#challenge_pop_start_time').timepicker({ 
                                    timeFormat: 'h:i A',
                                    minTime: today_date.getHours()+':'+'00'+':'+today_date.getSeconds(),
                                    maxTime: '11:30pm'
                                });
                            }
                            else{ 
                                $('#challenge_pop_start_time').remove();
                                $('#challenge_div_start_time').prepend('<input type="text" class="form-control" id="challenge_pop_start_time" placeholder="10.45" >');
                                $('#challenge_pop_start_time').timepicker({
                                    timeFormat: 'h:i A',
                                    maxTime: '11:30pm'
                                });
                            }
                            
                            var start_date_for_end = new Date($('#challenge_pop_start_date').val()); 
                                //start_date_for_end.setDate(start_date_for_end.getDate() + 1);
                                 
                            $("#challenge_pop_end_date").datepicker({
                                language: 'en',
                                minDate: start_date_for_end, // Now can select only dates, which goes after today 
                                onSelect: function(dateText, inst){
                                    $('#challenge_pop_end_date').removeClass("white");
                                    //console.log('change_end');

                                    var select_end_date = $('#challenge_pop_end_date').val();
                                    var select_start_time = __start_pop_time;
            
                                    var select_date     = new Date(select_end_date);
                                    var select_today_date   = new Date(start_date_for_end);
                                    var select_today_date_second = new Date(select_today_date.getFullYear(),select_today_date.getMonth(),select_today_date.getDate(),0,0,0,0);

                                    if(select_date.getTime() == select_today_date_second.getTime()){


                                        var hours = Number(select_start_time.match(/^(\d+)/)[1]);
                                        var minutes = Number(select_start_time.match(/:(\d+)/)[1]);
                                        var AMPM = select_start_time.match(/\s(.*)$/)[1];
                                        
                                        if(minutes == 0){
                                            minutes = 30;
                                        }else{
                                            if(AMPM == 'AM'){
                                                if(hours == '11' && minutes == '30'){
                                                    AMPM = 'PM';
                                                }
                                                if(hours == '12' && minutes == '30'){
                                                    hours = 0;
                                                    minutes = 0;
                                                }
                                                hours = hours + 1;
                                                minutes = 0;
                                            }else{
                                                if(hours == '11' && minutes == '30'){
                                                    hours = 11;
                                                    minutes = 59;
                                                }else{
                                                    hours = hours + 1;
                                                    minutes = 0;
                                                }
                                            }
                                             
                                        }
 
                                        if(AMPM == "PM" && hours<12) hours = hours+12;
                                        if(AMPM == "AM" && hours==12) hours = hours-12;
                                        var sHours = hours.toString();
                                        var sMinutes = minutes.toString();
                                        if(hours<10) sHours = "0" + sHours;
                                        if(minutes<10) sMinutes = "0" + sMinutes;
                                            
                                        $('#challenge_pop_end_time').remove();
                                        $('#challenge_div_end_time').prepend('<input type="text" class="form-control" id="challenge_pop_end_time" placeholder="10.45" >');
                                        
                                        if(sHours == '23' && minutes == '59'){
                                            $('#challenge_pop_end_time').timepicker({ 
                                                timeFormat: 'h:i A',
                                                minTime: sHours+':'+sMinutes+':'+'00',
                                                maxTime: '11:59pm'
                                            });
                                        }else{
                                            $('#challenge_pop_end_time').timepicker({ 
                                                timeFormat: 'h:i A',
                                                minTime: sHours+':'+sMinutes+':'+'00',
                                                maxTime: '11:30pm'
                                            });
                                        }
                                    }
                                    else{
                                        
                                        $('#challenge_pop_end_time').remove();
                                        $('#challenge_div_end_time').prepend('<input type="text" class="form-control" id="challenge_pop_end_time" placeholder="10.45" >');
                                        $('#challenge_pop_end_time').timepicker({
                                            timeFormat: 'h:i A',
                                            maxTime: '11:30pm'
                                        });
                                    }    
                                }
                            });
                            
                            
                        }
                    }
                });
            }
            else{
                cleanPopUpMessage();
                errorMessage = 'Please select category';
                str          = renderPopUpMessage('error', errorMessage);
                $('.challenge_zone_form').prepend(str);
                $("#challenge_pop_start_date").val('');
            }
            
            var sel_str_date = $('#challenge_pop_start_date').val();
            var sel_date     = new Date(sel_str_date);
            var today_date   = new Date();
            var today_date_second = new Date(today_date.getFullYear(),today_date.getMonth(),today_date.getDate(),0,0,0,0);
            if(sel_date.getTime() == today_date_second.getTime()){

                $('#challenge_pop_start_time').remove();
                $('#challenge_div_start_time').prepend('<input type="text" class="form-control" id="challenge_pop_start_time" placeholder="10.45" >');
                $('#challenge_pop_start_time').timepicker({ 
                    timeFormat: 'h:i A',
                    minTime: today_date.getHours()+':'+'00'+':'+today_date.getSeconds(),
                    maxTime: '11:30pm'
                });
            }
            else{
                $('#challenge_pop_start_time').remove();
                $('#challenge_div_start_time').prepend('<input type="text" class="form-control" id="challenge_pop_start_time" placeholder="10.45" >');
                $('#challenge_pop_start_time').timepicker({
                    timeFormat: 'h:i A',
                    maxTime: '11:30pm'
                });
            }
            
               
        }
    });
});


$(document).on('changeTime', '#challenge_pop_start_time', function() {
        $('#challenge_pop_start_time').addClass('white');
        setTimeout(function(){
            __start_pop_time = $('#challenge_pop_start_time').val();
            var start_time = __start_pop_time.split(" ");;
            $('#challenge_pop_start_time').val(start_time[0]);
            $('#challenge_pop_start_time_session').html(start_time[1]);    
            $('#challenge_pop_start_time').removeClass('white');                  
        },500);

        

    });

$(document).on('changeTime', '#challenge_pop_end_time', function() {
        $('#challenge_pop_end_time').addClass('white');
        setTimeout(function(){
            __end_pop_time = $('#challenge_pop_end_time').val();
            var end_time = __end_pop_time.split(" ");;
            $('#challenge_pop_end_time').val(end_time[0]);
            $('#challenge_pop_end_time_session').html(end_time[1]);  
            $('#challenge_pop_end_time').removeClass('white');          
        },500);
    }); 
    
$(document).on('focusout', '#challenge_pop_start_date', function (){
   
   $('#challenge_pop_end_date').val('');
   $('#challenge_pop_end_date').addClass("white");
});









$(document).ready(function(){
    $('#challenge_start_time').timepicker({ timeFormat: 'h:i A' });

    //$('#challenge_start_time').trigger('changeTime');
    
    $('#challenge_end_time').timepicker({ timeFormat: 'h:i A' });

    //$('#challenge_end_time').trigger('changeTime');
    
    var today = new Date();
    var set_end_date = new Date();
    
    $("#challenge_start_date").datepicker({
        language: 'en',
        minDate: today, // Now can select only dates, which goes after today
        onSelect: function(dateText, inst){
            $("#challenge_end_date").val('');
            catid = $("#challenge_zone_category").val();
            if(catid != ''){
                    $.ajax({
                    url: admin_url+'challenge_zone/check_start_date',
                    type: "POST",
                    data: {'startdate': dateText, catid: catid, challenge_id: challenge_id},
                    success: function(response){
                        data = $.parseJSON(response);
                        if(data['msg'] != ''){
                            $('#popUpMessage').html(data['msg']);
                        }else{
                            $('#popUpMessage').remove();
                        }
                        if(data['stat'] == 0){
                            cleanPopUpMessage();
                            errorMessage = data['msg'];
                            str          = renderPopUpMessage('error', errorMessage);
                            //$('.challenge_zone_form').prepend(renderPopUpMessage('error', errorMessage));
                            $('.challenge_zone_form').prepend(str);
                            scrollToTopOfPage();
                            $("#challenge_start_date").val('');
                        }else{
                            
                            var sel_str_date = $('#challenge_start_date').val();
            
                            var sel_date     = new Date(sel_str_date);
                            var today_date   = new Date();
                            var today_date_second = new Date(today_date.getFullYear(),today_date.getMonth(),today_date.getDate(),0,0,0,0);
                            if(sel_date.getTime() == today_date_second.getTime()){
                                
                                $('#challenge_pop_start_time').remove();
                                $('#challenge_div_start_time').prepend('<input type="text" class="form-control" id="challenge_pop_start_time" placeholder="10.45" >');
                                $('#challenge_start_time').timepicker({ 
                                    timeFormat: 'h:i A',
                                    minTime: today_date.getHours()+':'+'00'+':'+today_date.getSeconds(),
                                    maxTime: '11:30pm'
                                });
                            }
                            else{ 
                               
                                $('#challenge_pop_start_time').remove();
                                $('#challenge_div_start_time').prepend('<input type="text" class="form-control" id="challenge_pop_start_time" placeholder="10.45" >');
                                $('#challenge_start_time').timepicker({
                                    timeFormat: 'h:i A',
                                    maxTime: '11:30pm'
                                });
                            }
                            
                            var start_date_for_end = new Date($('#challenge_start_date').val()); 
                                //start_date_for_end.setDate(start_date_for_end.getDate() + 1);
                            $("#challenge_end_date").datepicker({
                                language: 'en',
                                minDate: start_date_for_end, // Now can select only dates, which goes after today 
                                onSelect: function(dateText, inst){
                                    $('#challenge_end_date').removeClass("white");
                                    var select_end_date = $('#challenge_end_date').val();
                                    var select_start_time = __start_time;
            
                                    var select_date     = new Date(select_end_date);
                                    var select_today_date   = new Date(start_date_for_end);
                                    var select_today_date_second = new Date(select_today_date.getFullYear(),select_today_date.getMonth(),select_today_date.getDate(),0,0,0,0);

                                    if(select_date.getTime() == select_today_date_second.getTime()){


                                        var hours = Number(select_start_time.match(/^(\d+)/)[1]);
                                        var minutes = Number(select_start_time.match(/:(\d+)/)[1]);
                                        var AMPM = select_start_time.match(/\s(.*)$/)[1];
                                        
                                        if(minutes == 0){
                                            minutes = 30;
                                        }else{
                                            if(AMPM == 'AM'){
                                                if(hours == '11' && minutes == '30'){
                                                    AMPM = 'PM';
                                                }
                                                if(hours == '12' && minutes == '30'){
                                                    hours = 0;
                                                    minutes = 0;
                                                }
                                                hours = hours + 1;
                                                minutes = 0;
                                            }else{
                                                if(hours == '11' && minutes == '30'){
                                                    hours = 11;
                                                    minutes = 59;
                                                }else{
                                                    hours = hours + 1;
                                                    minutes = 0;
                                                }
                                            }
                                             
                                        }
                                        
                                        if(AMPM == "PM" && hours<12) hours = hours+12;
                                        if(AMPM == "AM" && hours==12) hours = hours-12;
                                        var sHours = hours.toString();
                                        var sMinutes = minutes.toString();
                                        if(hours<10) sHours = "0" + sHours;
                                        if(minutes<10) sMinutes = "0" + sMinutes;
                                        //alert(sHours + ":" + sMinutes);
                                            
                                        $('#challenge_end_time').remove();
                                        $('#challenge_end_time_div').prepend('<input name="end_time" autocomplete="off" type="text" class="form-control" id="challenge_end_time" placeholder="10.45" >');
                                        if(sHours == '23' && minutes == '59'){
                                            $('#challenge_end_time').timepicker({ 
                                                timeFormat: 'h:i A',
                                                minTime: sHours+':'+sMinutes+':'+'00',
                                                maxTime: '11:59pm'
                                            });
                                        }else{
                                            $('#challenge_end_time').timepicker({ 
                                                timeFormat: 'h:i A',
                                                minTime: sHours+':'+sMinutes+':'+'00',
                                                maxTime: '11:30pm'
                                            });
                                        }
                                    }
                                    else{
                                        
                                        $('#challenge_end_time').remove();
                                        $('#challenge_end_time_div').prepend('<input name="end_time" autocomplete="off" type="text" class="form-control" id="challenge_end_time" placeholder="10.45" >');
                                        $('#challenge_end_time').timepicker({
                                            timeFormat: 'h:i A',
                                            maxTime: '11:30pm'
                                        });
                                    }    
                                }
                            });
                            
                            
                        }
                    }
                });
            }
            else{
                cleanPopUpMessage();
                errorMessage = 'Please select category';
                str          = renderPopUpMessage('error', errorMessage);
                $('.challenge_zone_form').prepend(str);
                $("#challenge_start_date").val('');
            }
            
            var sel_str_date = $('#challenge_start_date').val();
            var sel_date     = new Date(sel_str_date);
            var today_date   = new Date();
            var today_date_second = new Date(today_date.getFullYear(),today_date.getMonth(),today_date.getDate(),0,0,0,0);
            if(sel_date.getTime() == today_date_second.getTime()){
                
                $('#challenge_start_time').remove();
                $('#challenge_start_time_div').prepend('<input type="text" name="start_time" autocomplete="off" class="form-control" id="challenge_start_time" placeholder="10.45" >');
                $('#challenge_start_time').timepicker({ 
                    timeFormat: 'h:i A',
                    minTime: today_date.getHours()+':'+'00'+':'+today_date.getSeconds(),
                    maxTime: '11:30pm'
                });
            }
            else{
                
                $('#challenge_start_time').remove();
                $('#challenge_start_time_div').prepend('<input type="text" name="start_time" autocomplete="off" class="form-control" id="challenge_start_time" placeholder="10.45" >');
                $('#challenge_start_time').timepicker({
                    timeFormat: 'h:i A',
                    maxTime: '11:30pm'
                });
            }
            
            $("#challenge_end_date").val('');
            $('#challenge_start_time').trigger('changeTime');    
        }
    });
});

$(document).on('changeTime', '#challenge_start_time', function() {
        $('#challenge_start_time').addClass('white');
        setTimeout(function(){
            __start_time = $('#challenge_start_time').val();
            var start_time = __start_time.split(" ");;
            $('#challenge_start_time').val(start_time[0]);
            $('#challenge_start_time_session').html(start_time[1]);    
            $('#challenge_start_time').removeClass('white');                  
        },500);
    });

$(document).on('changeTime', '#challenge_end_time', function() {
        $('#challenge_end_time').addClass('white');
        setTimeout(function(){
            __end_time = $('#challenge_end_time').val();
            var end_time = __end_time.split(" ");;
            $('#challenge_end_time').val(end_time[0]);
            $('#challenge_end_time_session').html(end_time[1]);  
            $('#challenge_end_time').removeClass('white');          
        },500);
    }); 
    
$(document).on('focusout', '#challenge_start_date', function (){
   
   $('#challenge_end_date').val('');
   $('#challenge_end_date').addClass("white");
});

</script>
<?php include_once 'header.php'; ?>
<?php include_once "event_tab.php"; ?>
<link rel="stylesheet" href="<?php echo assets_url('css').'datepicker.min.css'; ?>">
<link rel="stylesheet" href="<?php echo assets_url('css').'jquery-ui.css'; ?>">
<link rel="stylesheet" href="<?php echo assets_url('css').'timepicker.css'; ?>">
<style>
.pt-20{padding-top:20px;}
section.base-cont-top.courses-tab {height: 47px !important;}
.courses-tab ol.nav li {border-bottom: unset !important;}
</style>
<section class="content-wrap base-cont-top-heading">
    <div class="left-wrap col-sm-12 pad0">
        <div class="container-fluid course-create-wrap">
            <div class="row-fluid course-cont">
                <div class="col-sm-12">
                    <div class="form-horizontal" id="ev_form">
                        <form class="form-horizontal" id="event_form" method="post" action="<?php echo admin_url('event/basic').$event['id']; ?>">
                            <input type="hidden" name="id" value="<?php echo $event['id']; ?>">
                            <div class="form-group">

                                <div class="col-sm-12">
                                    Event Name * : 
                                    <input type="text" class="form-control" maxlength="80" placeholder="eg: Event Name" name="ev_name" id="ev_name" value="<?php echo htmlentities($event['ev_name']) ?>" />
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-12">
                                    Event Description * : 
                                    <textarea class="form-control" id="ev_sdescription" name="ev_sdescription" value=""><?php echo $event['ev_description'] ?></textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-4">
                                    Event Date * : 
                                    <input type="text" placeholder="" name="ev_date" id="ev_date" class="form-control"  autocomplete="off" value="<?php echo date('m-d-Y',strtotime($event['ev_date'])); ?>" readonly="" style="background: #fff;">
                                </div>
                                <div class="col-sm-4">
                                    Event Time * : 
                                    <span id="time_wrapper">
                                        <input type="text" onkeypress="return false;" placeholder="" name="ev_time" id="ev_time" class="form-control"  autocomplete="off" value="<?php echo date('g:i A',strtotime($event['ev_time'])); ?>"  >
                                    </span>
                                    
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-4">
                                    <label>Event Type * : </label>
                                    <div>
                                        <label class="pad-top10">
                                            <input type="radio" <?php echo (($event['ev_type'] == '1')?'checked="checked"':'') ?> id="live_type" name="event_type" value="1">
                                            Live Event
                                        </label> &nbsp;
                                        <label>
                                            <input type="radio" <?php echo (($event['ev_type'] == '0')?'checked="checked"':'') ?> name="event_type" value="0">
                                            Offline Event
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-4 pb-30 pr-0" id="studio-wrapper" style="visibility:hidden">
                                <label> Studio *:</label>
                                <?php if(!empty($studios)) {
                                    ?>
                                    <select class="form-control" name="studio_id" id="studio_list">
                                        <option value="">SELECT</option>
                                        <?php
                                        foreach($studios as $studio) {
                                            $selected   = ($event['ev_studio_id'] == $studio['id'])? 'selected="selected"':'';
                                            ?>
                                            <option value="<?php echo $studio['id'] ?>" <?php echo $selected ?>> <?php echo $studio['st_dial_in_number']. ' - ' .$studio['st_name'] ?> </option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                    <?php
                                } ?>
                        </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <input type="button" onclick="saveEvent()" id="ev_submit" class="pull-right btn btn-green marg10" value="SAVE">
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- JS -->
<script type="text/javascript" src="<?php echo assets_url('js').'jquery-ui.min.js' ?>"></script>
<script type="text/javascript" src="<?php echo assets_url('js').'jquery.timepicker.min.js'; ?>"></script>
<script src="<?php echo assets_url() ?>js/redactor/js/redactor.js"></script>
<script src="<?php echo assets_url() ?>js/redactor/js/table.js"></script>
<script src="<?php echo assets_url() ?>js/redactor/js/alignment.js"></script>
<script>
    $(document).ready(function(){
        $('#ev_sdescription').redactor({
            imageUpload: admin_url + 'configuration/redactore_image_upload',
            source: false,
            minHeight: '20vh',
            maxHeight: '20vh',
            plugins: ['table', 'alignment'],
            callbacks: {
                imageUploadError: function (json, xhr) {
                    alert('Please select a valid image');
                    return false;
                }
            }
        });

        var today = new Date();
        
        $("#ev_date").datepicker({
            language: 'en',
            minDate: today,
            dateFormat: 'mm-dd-yy',
            autoClose: true,
            onSelect: function(dateText, inst) {

                var sel_date            = new Date(dateText);
                var today_date          = new Date();
                var today_date_second   = new Date(today_date.getFullYear(),today_date.getMonth(),today_date.getDate(),0,0,0,0);

                if(sel_date.getDate() == today_date_second.getDate()){
                    var current_time = today_date.getHours();
                    $('#ev_time').remove();
                    $('#time_wrapper').prepend('<input type="text" onkeypress="return false;" placeholder="" name="ev_time" id="ev_time" class="form-control"  autocomplete="off" >');
                    $('#ev_time').timepicker({
                        timeFormat: 'h:i A',
                        interval: 60,
                        minTime: (current_time+1).toString(),
                        maxTime: '11:30pm',
                        dynamic: false,
                        dropdown: true,
                        
                    });
                }else{

                    $('#ev_time').remove();
                    $('#time_wrapper').prepend('<input type="text" onkeypress="return false;" placeholder="" name="ev_time" id="ev_time" class="form-control"  autocomplete="off" >');
                    $('#ev_time').timepicker({
                        timeFormat: 'h:i A',
                        interval: 60,
                        maxTime: '11:30pm',
                        dynamic: false,
                        dropdown: true,
                    });
                }
            
            }
        }); 
        $('#ev_time').timepicker({ timeFormat: 'h:i A' });
        $('#ev_description').redactor({
            minHeight: 300
        });
    });
    if($('#live_type').is(':checked')) { 
        $('#studio-wrapper').css('visibility','visible');
    }
    
    $(document).ready(function(){
        $('input:radio[name="event_type"]').change(function(){
            if ($(this).val() == '1') {
                
                $('#studio-wrapper').css('visibility','visible');
            }else{
                $('#studio_list').val('');
                $('#studio-wrapper').css('visibility','hidden');
            }
        });
    });
    function saveEvent() {
        var eventName       = $.trim($('#ev_name').val());
        var eventDesc       = $.trim($('#ev_sdescription').val());
        var eventDate       = $.trim($('#ev_date').val());
        var eventTime       = $.trim($('#ev_time').val());
        var eventType       = $.trim($('input[name="event_type"]:checked').val());;
        var message         = [];

        if(eventName == '') {
            message.push('Event name cannot be empty 1');
        }
        if(eventDate == '') {
            message.push('Event date cannot be empty');
        }
        if(stripHtmlTags(eventDesc) == '') {
            message.push('Event description cannot be empty');
        }

        if(eventTime == '') {
            message.push('Event time cannot be empty');
        }

        if(eventType == '') {
            message.push('Event type cannot be empty');
        }

        if(message.length > 0 ) {
            var messageObject = {
                    'body': message.join('<br />'),
                    'button_yes': 'OK',
                };
            callback_warning_modal(messageObject);
            scrollToTopOfPage();
        } else {
           $('#event_form').submit();
        }
    }
    $(document).on('keypress', '#ev_date, #ev_time', function(e){
        e.preventDefault();
    });

    $('#ev_time').bind("cut copy paste",function(e) {
     e.preventDefault();
    });
</script>
<?php include_once 'footer.php'; ?>


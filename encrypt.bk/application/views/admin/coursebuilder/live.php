<?php include_once 'lecture_header.php'; 
    date_default_timezone_set('Asia/Kolkata');
?>
<link rel="stylesheet" href="<?php echo assets_url() ?>css/jquery-ui.css">
<link rel="stylesheet" href="<?php echo assets_url() ?>css/jquery.timepicker.min.css">
<link rel="stylesheet" href="https://cdn.plyr.io/3.4.3/plyr.css">
<style>
    .instruction-ul {
        margin-left: 11px;
    }
    .instruction-ul li {
        line-height: 2;
        list-style: outside none disc;
        text-align: left;
    }
    .instruction-ul a {
        color: #a40;
        font-style: italic;
    }
</style>
<?php 
    
    $checked       = 'checked="checked"';
    $status_button = 'btn-green';
    $status_label  = 'activate';
    $status_mark   = 'Inactive';
    $status_lang   = 'inactive';
    if($lecture['cl_status'] == 1)
    {
        $status_button = 'btn-orange';
        $status_label  = 'deactivate';
        $status_mark   = 'active';
        $status_lang   = 'active';
    }
?>
    <!-- Manin Iner container start -->
    <div class='course-bulder-content-inner'>
        <!-- top Header with drop down and action buttons -->
        <div class="buldr-header inner-buldr-header clearfix">
            <div class="pull-left">
                <!-- Header left items -->
                <div class="lecture-icon-big <?php echo $lecture_icons[$lecture['cl_lecture_type']]['parent'] ?>">
                    <i class="icon <?php echo $lecture_icons[$lecture['cl_lecture_type']]['child'] ?>"></i>
                </div>
                <h3><?php echo $lecture['cl_lecture_name']; ?></h3>
            </div>
            <!-- !.Header left items -->
            <div class="pull-right rite-side">
                <!-- Header right side items with buttons -->
                <a id="lecture_action_status_<?php echo $lecture['id'] ?>" class="btn <?php echo $status_button ?>"  onclick="changeLectureStatus('<?php echo $lecture['cl_status'] ?>', '<?php echo base64_encode(htmlentities($lecture['cl_lecture_name'])); ?>', '<?php echo $lecture['id'] ?>')"><?php echo strtoupper(lang($status_label)) ?></a>
                <a href="<?php echo admin_url('coursebuilder/home/'.$lecture['cl_course_id']) ?>"><button class="btn btn-red" id="back_button"><i class="icon icon-left"></i><?php echo lang('back') ?></button></a>
            </div>
        </div>
        <!-- !.top Header with drop down and action buttons -->

   
        <div class="col-sm-6 builder-left-inner"><!-- !.Left side bar section -->
            <!-- accordion starts here -->
            <div class="panel-group" id="accordion">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <div class="panel-title">
                        <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
                            <h4 class="coursebuilder-settingstab-title">Basic Settings</h4>
                        </a>
                        </div>
                    </div>
                    <div id="collapseOne" class="panel-collapse collapse in">
                        <div class="panel-body">
                                <!-- Form elements Left side :: lecture Documents-->
                            <div class="builder-inner-from" id="live_lecture_form">
                            <div class="form-group clearfix" style="<?php echo ($course_details['cb_has_lecture_image'] == 1) ? "display:block" : "display:none" ?>" >
                                        <label><?php echo lang('lecture_image') ?> :</label>
                                        <div class="section-create-wrapper text-center">
                                            <div class="section-card-container">
                                                <div class="section-card">

                                                <?php $lecture_image = explode('?', $lecture['cl_lecture_image']); 
                                                    
                                                    if(!isset($lecture_image[0]))
                                                    {
                                                        $lecture_image[0] = '';
                                                    }
                                                    if(isset($s3_lecture_image))
                                                    {
                                                        $lecture_image_url = $s3_lecture_image;
                                                    }
                                                    else
                                                    {
                                                        $lecture_image_url = ($lecture['cl_lecture_image'] && file_exists(course_lecture_upload_path_document(
                                                        array('course_id' => $lecture['cl_course_id'])). $lecture_image[0])) ? course_lecture_image_path(
                                                        array('course_id' => $lecture['cl_course_id'])) . $lecture['cl_lecture_image'] : default_course_path()."default-lecture.jpg";
                                                    }            
                                                ?>
                                                    <img id="lecture_image_add" data-id="<?php ?>" image_name="<?php echo ($lecture['cl_lecture_image']) ?  $lecture['cl_lecture_image'] : "default-lecture.jpg"; ?>" class="img-responsive" 
                                                    src="<?php echo $lecture_image_url; ?>">
                                                    <label>
                                                        <button class="btn btn-green section-img-upload-btn">CHANGE IMAGE</button>
                                                        <input id="lecture_logo_btn" name="lecture_image" class="fileinput logo-image-upload-btn lecture-image-upload"  accept="image/*" type="file">
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div style="text-align: center;margin: 5px 0px;">Allowed Minimum File Size : 500px x 354px</div>
                                    </div>
                                <div class="form-group clearfix">
                                    <label><?php echo lang('lecture_title') ?> *:</label>
                                    <input type="text" maxlength="80" placeholder="eg: Mathematical Calculations" id="lecture_name" name="lecture_name" value="<?php echo isset($lecture['cl_lecture_name'])? htmlentities($lecture['cl_lecture_name']):''; ?>" class="form-control">
                                </div>
                                <!-- Description area -->
                                <div class="form-group clearfix">
                                    <label><?php echo lang('description') ?> :</label>
                                    <textarea class="form-control" id="lecture_description" onkeyup="validateMaxLength(this.id)" maxlength="1000" name="lecture_description" ><?php echo isset($lecture['cl_lecture_description'])?$lecture['cl_lecture_description']:''; ?></textarea>
                                    <label class="pull-right" id="lecture_description_char_left"> <?php echo (1000 -strlen($lecture['cl_lecture_description'])).' '.lang('characters_left') ?></label>
                                </div> 
                                    
                                    <!-- !.Access and maximum access count -->
                                <div class="form-group clearfix">
                                    <label> Studio *:</label>
                                    <?php
                                        if(!empty($studios)) {
                                    ?>
                                        <select class="form-control" id="studio_list">
                                        <option value="">SELECT</option>
                                        <?php
                                        foreach($studios as $studio) {
                                            $selected   = ($lecture['ll_studio_id'] == $studio['id'])? 'selected="selected"':'';
                                            ?>
                                            <option value="<?php echo $studio['id'] ?>" <?php echo $selected ?>> <?php echo $studio['st_dial_in_number']. ' - ' .$studio['st_name'] ?> </option>
                                            <?php
                                        }
                                        ?>
                                        </select>
                                    <?php
                                    } ?>
                                </div>

                                <div class="form-group clearfix row">
                                    <div class="col-sm-5">
                                        <label>Date :</label>
                                        <input type="text" placeholder="" style="background-color:white" readonly class="form-control" id="schedule_date" value="<?php echo isset($lecture['ll_date']) ?date("d-m-Y", strtotime($lecture['ll_date'])) : '' ?>">
                                    </div>
                                    <?php 
                                        $time_drop       = array();
                                        $strtotime       = strtotime($lecture['ll_time']);
                                        $time_drop[]     = date('h', $strtotime); 
                                        $time_drop[]     = date('i', $strtotime); 
                                        $time_drop[]     = strtoupper(date('a', $strtotime)); 
                                    ?>
                                
                                    <div class="col-sm-4">
                                        <label>Start Time *:</label>
                                        <div class="input-group" id="live_lecture_start_time">
                                            <input type="text" class="form-control" onkeydown="event.preventDefault()" placeholder="10:45" id="start_time" value="<?php echo $time_drop[0] .':'.$time_drop[1]?>">
                                            <div class="input-group-addon" id="start_time_noon">
                                                <?php echo isset($time_drop[2])?$time_drop[2]:'PM' ?>
                                                <!-- <a href="javascript:void(0)" class="link-style my-italic font-normal">(IST)</a> -->
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-3">
                                        <label>Duration *:</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control"  placeholder="160" maxlength="3" id="duration" value="<?php echo $lecture['ll_duration'] ?>" style="padding: 9px 0 7px 5px;">
                                            <div class="input-group-addon">Min</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="preivew-area test-content" id="live_files_list_wrapper">
                                        <?php
                                        $live_files = $lecture['ll_files'];
                                        $live_files = json_decode($live_files, true);
                                        ?>
                                        <?php if(!empty($live_files)):?>
                                        <?php foreach ($live_files as $file_id => $l_files): ?>
                                        <div class="default-view-txt m0 mb10 test-folder" id="file_<?php echo $file_id ?>">
                                                <a target="_blank" href="<?php echo livefiles_path().$l_files['link'] ?>" title="View" class="test-folder-row" data-toggle="tooltip" data-placement="top" data-original-title="View">
                                                        <i class="icon icon-attach-1"></i>
                                                    </a>
                                                <a target="_blank" href="<?php echo livefiles_path().$l_files['link'] ?>"><?php echo $l_files['title'] ?></a>              
                                                <a href="javascript:void(0)" onclick="deleteLiveFiles('<?php echo $file_id ?>')" title="Delete" class="test-folder-row" data-toggle="tooltip" data-placement="top" data-original-title="Delete">
                                                        <i class="icon icon-trash-empty"></i>
                                                    </a>
                                        </div>
                                        <?php endforeach; ?>
                                        <?php endif; ?>
                                </div>
                                
                                <div class="text-right">
                                    <input type="hidden" id="lecture_id" value="<?php echo $lecture['id'] ?>">
                                    <input type="hidden" id="live_lecture_id" value="<?php echo $lecture['live_lecture_id'] ?>">
                                    <input type="hidden" id="section_id" value="<?php echo $lecture['cl_section_id'] ?>">
                                    <input type="hidden" id="course_id" value="<?php echo $lecture['cl_course_id'] ?>">
                                    <button type="button" onclick="saveLectureDetail()" class="btn btn-green"><?php echo lang('save') ?></button>
                                </div>

                            </div>
                            <!-- !.Form elements Left side :: lecture Documents-->
                        </div>
                    </div>
                </div>
            </div>
            <!-- accordion ends here -->
        </div>  <!-- !.Left side bar section -->


        <div class="col-sm-6 builder-right-inner"> <!-- right side bar section -->

            <!-- top Header with drop down and action buttons -->
            <div class="buldr-header inner-buldr-header clearfix">
                <div class="pull-left">
                    <!-- Header left items -->
                    <h3 class="right-top-header"><?php echo (!empty($live_recording))?lang('preview'):'';  ?></h3>
                    <!-- File name -->
                    <span class="right-file-name"></span>
                    <!-- !.File name -->
                </div>
                <!-- !.Header left items -->
                <!-- !.Header right items -->
                <div class="pull-right rite-side">
                    <span class="download-txt"></span>
                </div>
            </div>
            <!-- !.top Header with drop down and action buttons --> 
                    
            <?php 
                $current_date    = date('Y-m-d');
                $live_event_date = $lecture['ll_date'];

                $total_days =  round(abs(strtotime($current_date)-strtotime($live_event_date))/86400);
                switch ($total_days) {
                    case 0:
                        $day = lang('today');
                    break;
                    case 1:
                        $day = lang('tommorrow');
                    break;
                    default:
                        $day = lang('date').' '.$lecture['ll_date'];
                    break;
                }
                $current_date    = date('Y-m-d H:i:s');
                $live_event_date = $lecture['ll_date'];
                $time            = explode(':', $lecture['ll_time']);
                $live_event_date = $live_event_date.' '.$time[0].':'.$time[1];
                $live_event_date = date("Y-m-d H:i:s", strtotime($live_event_date));
                $difference      = strtotime($live_event_date) - strtotime($current_date);
                $diff_in_hrs     = round($difference/3600, 2);
                $diff_in_hrs     = ($diff_in_hrs>0)?round($difference/3600, 2).' '.lang('hour_left'):'';

            ?>
            <!-- PPT - presentations file preview will show here -->
            <div class="overflw-Y-scroll text-center"> 
                <video id="player" controls height="480" style="width:100%;" source=""></video>
            </div>
            <!--  !.PPT - presentations file preview will show here -->
        </div><!-- right side bar section -->
    </div>
    <!-- Modal pop up contents:: Delete Section popup-->
    <div class="modal fade alert-modal-new" id="common_message" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-small" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="form-group">
                        <b id="common_message_header"></b>
                        <p class="m0" id="common_message_content"></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-red" data-dismiss="modal">Ok</button>
                </div>
            </div>
        </div>
    </div>
    <!-- !.Modal pop up contents :: Delete Section popup-->
    <!-- Manin Iner container end -->
</body>
<!-- body end-->

</html>
    <?php  $admin = $this->auth->get_current_user_session('admin'); ?>
    <script>
        var __base_url      = '<?php echo base_url() ?>';
        var __livefilesPath = '<?php echo livefiles_path() ?>';
    
        function startOrStopModeratorLive()
        {
            var cisco_location = document.createElement('a');
            cisco_location.href=__base_url+'conference/?name=Moderator&userid=1000&room=<?php echo $lecture['live_lecture_id'] ?>&type=moderator&app=web';
            cisco_location.target = '_blank';
            cisco_location.id = 'cisco_location';
            document.body.appendChild(cisco_location);
            cisco_location.click();
            $('#cisco_location').remove();
        }

        function deleteLiveFiles(fileId)
        {
            $.ajax({
                url: admin_url+'coursebuilder/deleteLiveFiles',
                type: "POST",
                data:{"is_ajax":true, "live_id":$('#live_lecture_id').val(), "file_id":fileId},
                success: function(response) {
                    $('#file_'+fileId).remove();
                }
            });
        }
    

        $(document).ready(function(){
            $('[name="ll_mode"]').change(function(){
                var liveMode = $('[name="ll_mode"]:checked').val();
                if(liveMode=='1')
                {
                    $('#instruction_wrapper, #start_or_stop_live_btn').show();        
                    $('#start_or_stop_live_btn_cisco').hide();   
                    $('.mod-div').hide();       
                }
                else
                {
                    $('#instruction_wrapper, #start_or_stop_live_btn').hide();        
                    $('#start_or_stop_live_btn_cisco').show();  
                    $('.mod-div').show();       
                }
            });

        
            var sel_str_date        = $('#schedule_date').val();
            var match               = sel_str_date.split("-").reverse().join("-");
            var sel_str_date        = new Date(match);
            var sel_date            = new Date(sel_str_date);
            var today_date          = new Date();
            var today_date_second   = new Date(today_date.getFullYear(),today_date.getMonth(),today_date.getDate(),0,0,0,0);
            if(sel_date.getDate() == today_date_second.getDate()){
                $('#start_time').timepicker({ 
                    timeFormat: 'h:i A',
                    minTime: today_date.getHours()+':'+'00'+':'+today_date.getSeconds(),
                    maxTime: '11:30pm',
                            step: 30
                }).on('selectTime', function() {
                    $('#start_time').addClass('white');
                    __start_date = $(this).val();
                    var start_time = __start_date.split(" ");;
                    $('#start_time').val(start_time[0]);
                    $('#start_time_noon').html(start_time[1]);    
                    $('#start_time').removeClass('white');                  
                });
            }else{ 
                $('#start_time').timepicker({
                    timeFormat: 'h:i A',
                    // minTime: today_date.getHours()+':'+'00'+':'+today_date.getSeconds(),
                    maxTime: '11:30pm',
                    step: 30
                }).on('selectTime', function() {
                    $('#start_time').addClass('white');
                    __start_date = $(this).val();
                    var start_time = __start_date.split(" ");;
                    $('#start_time').val(start_time[0]);
                    $('#start_time_noon').html(start_time[1]);    
                    $('#start_time').removeClass('white');                  
                });
            }

            $("#schedule_date").on('selectTime', function() {
                $('#start_time').addClass('white');
                __start_date = $(this).val();
                var start_time = __start_date.split(" ");;
                $('#start_time').val(start_time[0]);
                $('#start_time_noon').html(start_time[1]);    
                $('#start_time').removeClass('white');                  
            });
        
        });
        $(document).ready(function(){
            $('#start_time').addClass('white');
            $('#start_time').val('<?php echo $time_drop[0].':'.$time_drop[1] ?>');
            $('#start_time_noon').html('<?php echo $time_drop[2] ?>');    
            $('#start_time').removeClass('white');                  
        });
    </script>

        <script
            src="https://cdn.polyfill.io/v2/polyfill.min.js?features=es6,Array.prototype.includes,CustomEvent,Object.entries,Object.values,URL"
            crossorigin="anonymous"
        ></script>
<script src="https://cdn.plyr.io/3.4.3/plyr.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        var __file='';
        var videoUrl = '<?php echo str_replace("/", "\/", $lecture['st_url'])  ?>';
        //console.log(videoUrl);
        var videoFile = videoUrl;//"https:\/\/www.youtube.com\/embed\/qyEzsAy4qeU";
        function getId(url) {
            var regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/;
            var match = url.match(regExp);

            if (match && match[2].length == 11) {
                return match[2];
            } else {
                return 'error';
            }
        }

        var videoId = getId(videoFile);

        const player = new Plyr('#player');
        if(videoId){
            player.source = {
                type: 'video',
                sources: [{
                    src: videoId, // From the YouTube video link
                    provider: 'youtube',
                },],
            };
        }
    });
   
   /* YouTube video link https://www.youtube.com/watch?v=aqz-KE-bpKQ */
</script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/jquery.timepicker.min.js"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/jquery-ui.min.js"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/live.js"></script>
<?php $accepted_files = array('mp4', 'flv', 'avi', 'f4v', 'doc', 'docx', 'pdf', 'ppt', 'pptx', 'odt', 'jpeg', 'jpg', 'png'); ?>
<script>
    var __controller         = '<?php echo $this->router->fetch_class() ?>';
    var __allowed_files      = $.parseJSON('<?php echo json_encode($accepted_files) ?>');
</script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/system.js"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/language.js"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/lecture_details.js"></script>
<script src="<?php echo assets_url() ?>js/app.js" type="text/javascript"></script>
<script type="text/javascript">

    $(document).ready(function(){
        App.initEqualizrHeight(".builder-left-inner", ".builder-right-inner");
    });

    $(function(){
        var today = new Date();
        $("#descriptive_submission_date").datepicker({
            language: 'en',
            minDate: today,
            dateFormat: 'dd-mm-yy',
            autoClose: true
        });
    });
    
</script>
<?php include_once 'lecture_footer.php'; ?>
<script>

    $( "#start_time" ).focus(function() {
        var today_date   = new Date();
        var current_time = today_date.getHours();
        // $('#start_time').remove();
        // $('#live_lecture_start_time').prepend('<input type="text" class="form-control" id="start_time" placeholder="10.45" >');
        $('#start_time').timepicker({
            timeFormat: 'h:i A',
            interval: 60,
            minTime: (current_time+1).toString(),
            maxTime: '11:30pm',
            dynamic: false,
            dropdown: true,
            
        }).on('selectTime', function() {

            $('#start_time').addClass('white');
            __start_date = $(this).val();
            var start_time = __start_date.split(" ");;
            $('#start_time').val(start_time[0]);
            $('#start_time_noon').html(start_time[1]);    
            $('#start_time').removeClass('white');                  
        });

    });

    $("#schedule_date").datepicker({
        language: 'en',
        // dateFormat: 'dd-mm-yy',
        minDate: new Date(),// Now can select only dates, which goes after today
        onSelect: function(dateText, inst) {

            var sel_date            = new Date(dateText);
            // var match = sel_str_date.split("-").reverse().join("-");
            var today_date          = new Date();
            var today_date_second   = new Date(today_date.getFullYear(),today_date.getMonth(),today_date.getDate(),0,0,0,0);

            if(sel_date.getDate() == today_date_second.getDate()){
                var current_time = today_date.getHours();

                $('#start_time').remove();
                $('#live_lecture_start_time').prepend('<input type="text" class="form-control" id="start_time" placeholder="10.45" >');
                $('#start_time').timepicker({
                    timeFormat: 'h:i A',
                    interval: 60,
                    minTime: (current_time+1).toString(),
                    maxTime: '11:30pm',
                    dynamic: false,
                    dropdown: true,
                    
                }).on('selectTime', function() {

                    $('#start_time').addClass('white');
                    __start_date = $(this).val();
                    var start_time = __start_date.split(" ");;
                    $('#start_time').val(start_time[0]);
                    $('#start_time_noon').html(start_time[1]);    
                    $('#start_time').removeClass('white');                  
                });
            }else{

                $('#start_time').remove();
                $('#live_lecture_start_time').prepend('<input type="text" class="form-control" id="start_time" placeholder="10.45" >');
                $('#start_time').timepicker({
                    timeFormat: 'h:i A',
                    interval: 60,
                    maxTime: '11:30pm',
                    dynamic: false,
                    dropdown: true,
                }).on('selectTime', function() {

                    $('#start_time').addClass('white');
                    __start_date = $(this).val();
                    var start_time = __start_date.split(" ");;
                    $('#start_time').val(start_time[0]);
                    $('#start_time_noon').html(start_time[1]);    
                    $('#start_time').removeClass('white');                  
                });
            }

        }    
    });   
</script>
<script>
    function startOrStopCiscoLive()
    {
        var cisco_location = document.createElement('a');
        cisco_location.href=__base_url+"conference/?name=<?php echo $admin['us_name'] ?>&userid='+<?php echo $admin['id'] ?>+'&room=<?php echo $lecture['live_lecture_id'] ?>&type=provider&app=web";
        cisco_location.target = '_blank';
        cisco_location.id = 'cisco_location';
        document.body.appendChild(cisco_location);
        cisco_location.click();
        $('#cisco_location').remove();
    }
</script>
<script>
    var cb_has_lecture_image =  '<?php echo $course_details['cb_has_lecture_image'] ?>';
    var __course_loaded_img  = $('#lecture_image_add').attr('src');
    $(function(){
        $("#lecture_logo_btn").change(function() {
            readImageData(this); //Call image read and render function
        });
    });
    function readImageData(imgData) { 
        console.log(imgData);
        if (imgData.files && imgData.files[0]) {
            var readerObj = new FileReader();
            
            readerObj.onload = function(element) {
                var img = new Image;
                $('#lecture_image_add').attr('src', element.target.result);
                var image_alowed_types = ['image/png', 'image/jpg', 'image/gif', 'image/jpeg', 'image/png'];
                if(jQuery.inArray($('#lecture_logo_btn')[0].files[0].type, image_alowed_types) < 0){
                    lauch_common_message('Image type', 'The file you have chosen is not allowed.');
                    $('#lecture_image_add').attr('src',__course_loaded_img);
                    $('#lecture_logo_btn').val("");
                    // $('#lecture_image_add').attr('data-id', 'default.jpg');
                    return false;
                }
                img.onload = function() {
                    if(img.width < 500 || img.height < 354){
                        lauch_common_message('Image Size', 'The image you have choosen is too small and cannot be uploaded.');
                        $('#lecture_image_add').attr('src',__course_loaded_img);
                        $('#lecture_logo_btn').val("");
                        // $('#lecture_image_add').attr('data-id', 'default.jpg');
                        return false;
                    }
                };

                img.src = element.target.result;
            }
            readerObj.readAsDataURL(imgData.files[0]);
        }
    }
            </script>
  
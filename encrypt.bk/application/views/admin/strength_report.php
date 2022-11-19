<?php include_once 'header.php'; ?>
<?php include_once('report_tab.php') ?>
<style>
.progress-box .progress.strength-progress{ background-color: #dddddd;}
</style>
<section class="content-wrap create-group-wrap settings-top reports-left">
    <!-- LEFT CONTENT --> <!-- STARTS -->
    <!-- ===========================  -->
    <div class="col-sm-12 pad0 settings-left-wrap">
        <!-- Nav section inside this wrap  --> <!-- START -->
        <!-- =========================== -->
        
        
   <div class="col-sm-12 nav-content faculty-nav-content width-130p">
            <div class="row">
                <div class="rTable content-nav-tbl" style="">
                    <div class="rTableRow">

                        <div class="rTableCell dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"  data-role-id="0"><span id="dropdown_role_text">Select Course</span><span class="caret"></span></a>
                            <ul class="dropdown-menu white">
                            <li><a onclick="loadCategory('<?php echo base64_encode(0) ?>','<?php echo base64_encode(-1) ?>')" href="javascript:void(0)">Select Course</a></li>
								<?php if(isset($courses)&&!empty($courses)):
                                    foreach($courses as $course_key => $course): ?>
                                        <li><a onclick="loadCategory('<?php echo base64_encode($course['id']) ?>','<?php echo base64_encode($course_key) ?>')" href="javascript:void(0)"><?php echo $course['cb_title'] ?></a></li>
                                   <?php endforeach;
                                endif; ?>
                            </ul>
                        </div>
                        <div class="rTableCell">
                            <div class="input-group">
                                <input class="form-control srch_txt" id="student_keyword" placeholder="Search by name" type="text">
                                <a class="input-group-addon" id="student_search">
                                    <i class="icon icon-search"> </i>
                                </a>
                            </div> 
                        </div>
                        
                        <div class="rTableCell">
                            <div class="save-btn"><button class="pull-right btn btn-green" onclick="export_excel();">EXPORT</button></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>     
        
        
        
        
        <!-- Nav section inside this wrap  --> <!-- END -->
        <!-- =========================== -->

        <!-- Group content section  -->
        <!-- ====================== -->

        <div class="col-sm-12 group-content course-cont-wrap group-top list-tp"> 
            <div class="table course-cont rTable list-cont" style="" id="group_wrapper">
            
            </div>
        </div>
        <!-- ====================== -->
        <!-- Group content section  -->
    </div>

    <div class="col-sm-6 pad0 right-content list-right">
        <div class="container-fluid right-box list-bx">
            <div class="row">
                <div class="col-sm-12 rel-top80 course-cont-wrap"> 
                    <div class="table course-cont rTable right-table" style="" id="group_detail_wrapper">                                                  
                    
                    
                    </div>

                </div>
            </div>
        </div>

    </div>
    <!-- ==========================  -->
    <!--  LEFT CONTENT--> <!-- ENDS -->
</section>
<script type="text/javascript" src="<?php echo assets_url() ?>js/jquery.slimscroll.min.js"></script>
<script type="text/javascript">
    var __course_selected   = 0;
    var __stuedent_selected = 0;
    var __stuedents         = new Object();
    __stuedents             = atob('<?php echo base64_encode(json_encode(isset($students)?$students:'')) ?>');
    var __default_image_url = '<?php echo default_user_path(); ?>';
    var __image_url         = '<?php echo user_path(); ?>';
    var __courses           = atob('<?php echo base64_encode(json_encode($courses)) ?>');
    __courses               = jQuery.parseJSON(__courses);
    var __admin_url         = '<?php echo base64_encode(admin_url()); ?>';
    __admin_url             = atob(__admin_url);
    var __keyword           = '';
    var __offset            = 1;
    var __limit             = '<?php echo $limit; ?>';
    __limit                 = parseInt(__limit);


    $( document ).ready(function() {
        __stuedents         = $.parseJSON(__stuedents);
        $('#group_wrapper').html(renderStudents(__stuedents));
        if(__stuedents.length == __limit){
            $('#group_wrapper').after('<div><center><a onclick="show_more();" id="loadMore">Load more</a></center></div>');
        }
    });

    $('#student_search').on('click', function () {
        search();
    });

    function search(){
        __keyword = $('#student_keyword').val();
        __offset  = 1;
        /*if(__course_selected != 0){
            send_category_request();
        }else{
            $('#group_detail_wrapper').html('<div id="popUpMessage" class="alert alert-danger">    <a data-dismiss="alert" class="close">×</a>Choose a course.</div>');
        }*/

        send_category_request();
    }

    $('#student_keyword').keyup(function(e){
        if(e.keyCode == 13)
        {
            search();
        }
    });

    function loadCategory(category_id,key){
        var index = atob(key);
        $('#student_keyword').val('');
        __keyword = '';
        __offset  = 1;
        __stuedent_selected = 0;
        __course_selected = atob(category_id);
        if(index == -1){
            $('#dropdown_role_text').html('Select Course');
        }else{
            $('#dropdown_role_text').html(__courses[index]['cb_title']);
        }
        $('#group_wrapper').html('');
        send_category_request();
    }

    function show_more(){
        __offset++;
        $('#loadMore').html('Loading...');
        send_category_request();
    }

    function send_category_request(){
        $.ajax({
            url: __admin_url+'report/enroled_students_ajax',
            type: "POST",
            data:{"is_ajax":true, "course_id":__course_selected,"student_name":__keyword,'limit':__limit,'offset':__offset},
            success: function(response) {
                var data                    = $.parseJSON(response);     
                if(data['success'] == 1){
                    __stuedents             = data['students'];
                    if(__offset == 1){
                        $('#group_wrapper').html(renderStudents(data['students']));
                    }else{
                        $('#group_wrapper').append(renderStudents(data['students']));
                    }
                    $('#group_detail_wrapper').html('');
                    if(data['students'].length == __limit){
                        $('#loadMore').html('Load more').show();
                    }else{
                        $('#loadMore').hide();
                    }
                }else{
                    show_message('error',data['message']);
                }  
            }
        }); 
    }

    function renderStudents(students){
        var renderHtml = '';

        $.each(students, function(studentKey, student )
        {
            renderHtml += '<div class="list-row" id="list'+student["id"]+'">';
            renderHtml += '<div class="list-col">';
            renderHtml += '<span class="wrap-mail ellipsis-hidden">';
            renderHtml += '<div class="ellipsis-style">';
            renderHtml += '<span class="icon-wrap-round img">';
            if(student['us_image'] == "default.jpg"){
                renderHtml += '<img src="'+__default_image_url+student['us_image']+'">';
            }else{
                renderHtml += '<img src="'+__image_url+student['us_image']+'">';
            }
            renderHtml += '</span>';
            renderHtml += '<a onclick="loadStudent('+student["id"]+','+studentKey+')" href="javascript:void(0)">'+student["us_name"]+'</a>';
            renderHtml += '</div>';
            renderHtml += '</span>';
            renderHtml += '</div>';
            renderHtml += '</div>'; 
        });
        return renderHtml;
    }

    function loadStudent(studentId,key){
        $('#group_detail_wrapper').html('<div class="col-md-12 "><h3>Loading...</h3></div>');
        __stuedent_selected = studentId;
        $('.list-row').removeClass('active');
        $('#list'+__stuedent_selected).addClass('active');
        $.ajax({
            url: __admin_url+'report/topic_wise_progress',
            type: "POST",
            data:{"is_ajax":true, "course_id":__course_selected,"user_id":__stuedent_selected},
            success: function(response) {
                var data                    = $.parseJSON(response);     
                if(data['success'] == 1){
                    //$('#group_wrapper').html(renderStudents(data['students']));
                    $('#group_detail_wrapper').html(renderProgress(data['progress'],__stuedents[key]));
                        $('.progress-bar-percentage-animator').each(function(){
                            $(this).animate({
                                width: $(this).attr('aria-valuenow')+'%'
                            }, 300 );
                        });
                }else{
                    show_message('error',data['message']);
                }
            }
        }); 
    }

    function renderProgress(progress,student){
        var renderHtml = '';
        var percentage = 0;
        renderHtml += '<div class="col-md-12 assignment-image">';
        renderHtml += '<span class="icon-wrap-round img assignment-img">';
        if(student["us_image"] == 'default.jpg'){
            renderHtml += '<img src="'+__default_image_url+student["us_image"]+'" width="90">';
        }else{
            renderHtml += '<img src="'+__image_url+student["us_image"]+'" width="90">';
        }
        renderHtml += '</span>';
        renderHtml += '<h3>'+student["us_name"]+'</h3>';
        renderHtml += '</div>';

        $.each(progress, function(pKey, progres )
        {
            progres['scored_mark'] = progres['scored_mark']==null?0:progres['scored_mark'];
            progres["duration"]    = progres["duration"]==null?0:progres["duration"];
            percentage = (progres['scored_mark']/progres['total_mark'])*100;
            percentage = percentage==100?percentage:parseFloat(percentage).toFixed(2);
            renderHtml += '<div class="strength-block">';
            renderHtml += '<div class="col-md-12 topic-name"><b>'+progres["qc_category_name"]+'</b>:('+secondsToHms(progres["duration"])+' avg)</div>';
            renderHtml += '<div class="col-md-12 progress-box">';
            renderHtml += '<div class="progress-left">'+Math.round(percentage)+'%</div>';
            renderHtml += '<div class="progress sml-progress strength-progress">';
            renderHtml += '<div class="progress-bar progress-bar-percentage-animator" aria-valuenow="'+percentage+'" role="progressbar" aria-valuemin="0" aria-valuemax="100">';
            renderHtml += '<span class="sr-only">'+percentage+'% Complete</span>';
            renderHtml += '</div>';
            renderHtml += '</div>';
            renderHtml += '<div class="progress-right">'+Math.round(100-percentage)+'%</div>';
            renderHtml += '</div>';
            renderHtml += '</div>';
        });
        
        return renderHtml;
    }
    function show_message(type,message){
        switch(type){
            case 'success':
                $('#group_detail_wrapper').html('<div id="popUpMessage" class="alert alert-success"><a data-dismiss="alert" class="close">×</a>'+message+'</div>');
            break;

            case 'error':
                $('#group_detail_wrapper').html('<div id="popUpMessage" class="alert alert-danger"><a data-dismiss="alert" class="close">×</a>'+message+'</div>');
            break;
        }
    }

    function export_excel(){
        var exportuser = '';
        if(__stuedent_selected == 0){
            show_message('error','Choose a student to export.');
            return false;
        }

        exportuser = btoa(__stuedent_selected);
        window.open(__admin_url+'report/strength_report_export/'+exportuser);

    }

    function secondsToHms(d) {
        d = Number(d);
        var h = Math.floor(d / 3600);
        var m = Math.floor(d % 3600 / 60);
        var s = Math.floor(d % 3600 % 60);
        var mDisplay = ("0" + m).slice(-2)+".";
        var sDisplay = ("0" + s).slice(-2);
        return mDisplay + sDisplay; 
    }

</script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/multi-select/jquery.tokenize.js"></script>
<script>
$(document).ready(function(e) {
    $(function(){
        $('.right-box').slimScroll({
            height: '100%',
            width: '100%',
            wheelStep : 3,
            distance : '10px'
        });
    });
});
</script>
<?php include_once 'footer.php'; ?>

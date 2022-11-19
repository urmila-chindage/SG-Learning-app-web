<?php include_once 'training_header.php'; ?>

<script type="text/javascript" src="<?php echo assets_url() ?>js/datepicker.js"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/datepicker.en.js"></script>
<style>
.faculty-role-holder {
    color: #65367d;
    font-size: 11px;
    font-weight: 600;
    background: #f6f8fa;
    line-height: 1px;
    height: 21px;
    vertical-align: middle;
    display: inline-block;
    padding: 8px 6px;
    border-radius: 10px;
    border: 1px solid #65367d;
}
.faculty-list_wrapper ul li{
    border-bottom: 1px solid #ccc;
}
.faculty-name-holder{
    display: inline-block;
    padding: 7px 0;
}
.faculty-role-wrapper{
    padding: 7px 0px 0px 0;
}
.faculty-role-holder-hover {
    cursor: pointer;
}

.faculty-role-holder-hover:hover {
    background: #65367d;
    color: #fff;
    border-color: #65367d;
}
</style>
<?php //echo '<pre>'; print_r($permission);die; ?>

<section class="content-wrap cont-course-big top-spacing content-wrap-align">
    <!-- LEFT CONTENT --> <!-- STARTS -->
    <!-- ===========================  -->
    <div class="left-wrap col-sm-12 pad0">
        <!-- Nav section inside this wrap  --> <!-- START -->
        <!-- =========================== -->

        <div class="container-fluid course-create-wrap">
            <div class="row-fluid course-cont">
                <div class="col-sm-12">
                    <div class="form-horizontal" id="course_div_youtube">
                        <?php include_once('messages.php') ?>
                        <form class="form-horizontal" id="course_form_youtube" method="post" action="<?php echo admin_url('course_settings/advanced/'.$course['id']); ?>">
                            <!-- Select Box  -->
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <?php if(isset($permission['add']) && $permission['add']): ?>
                                    <div class="buldr-header text-right clearfix row">
                                        <div class="pull-right">
                                            <a href="javascript:void(0)" class="btn btn-green pull-right" data-toggle="modal" data-target="#add-teacher" id="add-tutor-advanced">ASSIGN NEW FACULTY</a>
                                        </div>
                                    </div>     
                                    <?php endif; ?>                          

                                <div class="clearfix">
                                        <div class="faculty-list_wrapper">
                                            <ul id="list-teacher-image"></ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="savenext" id="savenextform" value="0">
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
        
<?php 
 function generate_youtube_url($url=false)
{
    //if(!$url)
    //{
    //    return $url;
    //}
    //$query_str = parse_url($url, PHP_URL_QUERY);
    //parse_str($query_str, $query_params);
    //return 'https://www.youtube.com/embed/'.$query_params['v'];
     
    $pattern = 
       '%^# Match any youtube URL
       (?:https?://)?  # Optional scheme. Either http or https
       (?:www\.)?      # Optional www subdomain
       (?:             # Group host alternatives
         youtu\.be/    # Either youtu.be,
       | youtube\.com  # or youtube.com
         (?:           # Group path alternatives
           /embed/     # Either /embed/
         | /v/         # or /v/
         | /watch\?v=  # or /watch\?v=
         )             # End path alternatives.
       )               # End host alternatives.
       ([\w-]{10,12})  # Allow 10-12 for 11 char youtube id.
       $%x'
       ;
    $result = preg_match($pattern, $url, $matches);
    if ($result) {
        //return $matches[1];
        return 'https://www.youtube.com/embed/'.$matches[1];
    }
    return false;
}
 ?>


 <script>
 var __tutorAction      = '';
 var __selectedTutor    = new Array();
 var course_id          = '<?php echo $course['id'] ?>';
 $(document).ready(function(){
    $('.message_container').delay(3000).fadeOut();
     renderRoleFilters();
    $("#add-tutor-advanced").on('click', function(){
        addTeacherToCourse();
    });

    $.ajax({
        url: admin_url+'course_settings/get_assigned_tutors',
        type: "POST",
        data:{"is_ajax":true, 'course_id': course_id},
        success: function(response) {
            var data        = $.parseJSON(response);
            var tutorHtml   = '';
            if(Object.keys(data).length > 0) {
                $.each(data, function(key, faculty) {
                    __selectedTutor.push(faculty['id']);
                    tutorHtml += '<li>';
                    tutorHtml += '    <div class="faculty-name-holder"><span>'+faculty['us_name']+'</span></div>';
                    tutorHtml += '    <div class="pull-right faculty-role-wrapper" >';
                    tutorHtml += '        <span class="faculty-role-holder">'+faculty['rl_name']+'</span>';
                    <?php if(isset($permission['delete']) && $permission['delete']): ?>
                    tutorHtml += '        <span onclick="removeFacultyFromCourse('+faculty['id']+', \''+btoa(faculty['us_name'])+'\')" class="faculty-role-holder faculty-role-holder-hover" data-toggle="tooltip" data-placement="left" data-original-title="Unassign '+faculty['us_name']+'">x</span>';
                    <?php endif; ?>
                    tutorHtml += '    </div>';
                    tutorHtml += '</li>';
                });
            }
            $("#list-teacher-image").html(tutorHtml);
            initToolTip();
        }
    });
});
var __facultyRoles = $.parseJSON(atob('<?php echo base64_encode(json_encode($faculty_roles)); ?>'));
function renderRoleFilters() {
    var roleFilters = '';
    roleFilters += '<li><a href="javascript:void(0)" onclick="filter_faculty_by(0)" id="faculty_filter_0">All Faculties</a></li>';
    if(Object.keys(__facultyRoles).length > 0 ) {
        $.each(__facultyRoles, function(key, role){
            roleFilters += '<li><a href="javascript:void(0)" id="faculty_filter_'+role['id']+'" onclick="filter_faculty_by(\''+role['id']+'\')">'+role['rl_name']+'</a></li>';
        });
    }
    $('#role_filters').html(roleFilters);
}

function filter_faculty_by(roleId) {
    $('#popUpMessage').remove();
    $('#filter_dropdown_text').html($('#faculty_filter_'+roleId).text()+'<span class="caret"></span>');
    if(roleId == 0) {
        $('.faculty_wrappers').show();
    } else {
        $('.faculty_wrappers').hide();
        $('.faculty_under_'+roleId).show();
       
    }
    var wrapperCount = $('.faculty_wrappers:visible').length;
    if(wrapperCount<=0){
        var errorMessage = 'No Faculty Found.';
        $('#add-teacher #get_tutor_list').prepend(renderPopUpMessage('error', errorMessage));
    }
}

function removeFacultyFromCourse(facultyId, facultyName) {
    var messageObject = {
        'body':'Are you sure to unassign <b>'+atob(facultyName)+'</b> from this course?',
        'button_yes':'UNASSIGN', 
        'button_no':'NO',
        'continue_params':{'faculty_id':facultyId},
    };
    callback_warning_modal(messageObject, removeFacultyFromCourseConfirmed);
}
function removeFacultyFromCourseConfirmed(param)
{
    for (var i = __selectedTutor.length; i--;) {
        if (__selectedTutor[i] == param.data.faculty_id) {
            __selectedTutor.splice(i, 1);
        }
    }
    __tutorAction = 'unassigned';
    import_tutor();
}


function addTeacherToCourse() {
    filter_faculty_by(0);
    $.ajax({
        url: admin_url+'course_settings/get_restricted_access_faculties',
        type: "POST",
        data:{"is_ajax":true},
        success: function(response) {
            var tutorHtml       = '';
            var tutors          = $.parseJSON(response);
            var checked         = 'checked="checked"';
            if(Object.keys(tutors).length > 0) {
                $.each(tutors, function(key, faculty) {
                    if(inArray(faculty['id'],__selectedTutor) == false) {
                        tutorHtml += '<div class="checkbox-wrap group-filter faculty_wrappers faculty_under_'+faculty['us_role_id']+'">';
                        tutorHtml += '    <span class="chk-box">';
                        tutorHtml += '        <label class="font14 inline"><input id="tutor_details_'+faculty['id']+'" value="'+faculty['id']+'" type="checkbox" class="tutor-checkbox"><span class="inline-flex max-width-70">'+faculty['us_name']+'</span></label>';
                        tutorHtml += '    </span>';
                        tutorHtml += '    <span class="email-label pull-right">'+faculty['rl_name']+'</span>';
                        tutorHtml += '</div>';                 
                    }
                });
                
            }
            $('#get_tutor_list').html(tutorHtml);
            setTimeout(function(){
                var wrapperCount = $('.faculty_wrappers:visible').length;
                if(wrapperCount<=0){
                    var errorMessage = 'No Faculty Found.';
                    $('#add-teacher #get_tutor_list').prepend(renderPopUpMessage('error', errorMessage));
                }
            },500);
            
        }
    });
}

$(document).on('change', '.tutor-checkbox', function() {
    var tutorId = $(this).val();
    if ($(this).is(':checked') == true) {
        __selectedTutor.push(tutorId);
    } else {
        removeArrayIndex(__selectedTutor, tutorId);
    }
});

function import_tutor_confirmed() {
    if( $('.tutor-checkbox:checked').length === 0 ) {
        var messageObject = {
            'body':'Choose atleast one faculty to assign',
            'button_yes':'OK', 
            'prevent_button_no': true
        };
        callback_danger_modal(messageObject);
        return false;
    } 
    __tutorAction = 'assigned';
    import_tutor();
}

function import_tutor() {
    if(__selectedTutor.length==1){

    }
    $.ajax({
        url: admin_url+'course_settings/save_tutor',
        type: "POST",
        data:{"tutors":JSON.stringify(__selectedTutor),"tutor_action":__tutorAction, "id": course_id, "is_ajax":true},
        success: function(response) {
            //console.log(response);return;exit;
            var data        = $.parseJSON(response);
            var tutorHtml   = '';
            __selectedTutor = new Array();
            if(Object.keys(data).length > 0) {
                $.each(data, function(key, faculty) {
                    __selectedTutor.push(faculty['id']);
                    tutorHtml += '<li>';
                    tutorHtml += '    <div class="faculty-name-holder"><span>'+faculty['us_name']+'</span></div>';
                    tutorHtml += '    <div class="pull-right faculty-role-wrapper" >';
                    tutorHtml += '        <span class="faculty-role-holder">'+faculty['rl_name']+'</span>';
                    <?php if(isset($permission['delete']) && $permission['delete']): ?>
                    tutorHtml += '        <span onclick="removeFacultyFromCourse('+faculty['id']+', \''+btoa(faculty['us_name'])+'\')" class="faculty-role-holder faculty-role-holder-hover" data-toggle="tooltip" data-placement="left" data-original-title="Unassign '+faculty['us_name']+'">x</span>';
                    <?php endif; ?>
                    tutorHtml += '    </div>';
                    tutorHtml += '</li>';
                });
            }
            $('#get_tutor_list').html('');
            $("#list-teacher-image").html(tutorHtml);
            initToolTip();
            filter_faculty_by(0);
            $("#add-teacher").modal('hide');
            $("#common_message_advanced").modal('hide');
            // __selectedTutor=[];
        },
        error: function (){
            $("#add-teacher").modal('hide');
            $("#common_message_advanced").modal('hide');
        }
    });
}
</script>
<?php include_once 'footer.php';?>
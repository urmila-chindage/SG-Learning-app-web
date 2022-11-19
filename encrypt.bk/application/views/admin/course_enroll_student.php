<?php include_once "header.php";?>
<script>
    var __user_selected = new Array();
    
</script>
<style>
    .question-bank-bulk{
        padding-bottom: 0px !important;
    }
    .question-count{
        overflow: visible !important;
    }
    iframe{ border:none;}
    .user-selector{vertical-align: bottom !important;}
</style>


<!-- SIDEBAR RIGHT SIDE FIXED --> <!-- START -->
<div class="right-wrap base-cont-top container-fluid pull-right right-wrap-height-adjust" style="top:95px!important;">
        <a href="javascript:void(0)" id="enroll_user_confirmed" class="btn btn-big btn-green selected full-width-btn" onclick="enrollStudents()">
            ENROLL STUDENTS 
        </a>
        <a href="<?php echo admin_url('course/users/' . $course_id) ?>" class="btn btn-big btn-blue full-width-btn">
            CANCEL
        </a>
    <!--  Adding list group  --> <!-- END  -->
</div>
<!-- SIDEBAR RIGHT SIDE FIXED --> <!-- END -->



<section class="content-wrap" style="top:94px;">

    <!-- Nav section inside this wrap  --> <!-- START -->
    <!-- =========================== -->
<div class="container-fluid nav-content nav-course-content add-question-block" style="width:100% !important;top: 0px !important;height: 50px;">
    <div class="col-sm-12 bottom-line question-head">
        <h3 class="question-title">Enroll Students</h3>
        <span class="cb-close-qstn" style="right: 30px;"><i class="icon icon-cancel-1" onclick="location.href='<?php echo admin_url('course/users/' . $course_id) ?>'"></i></span>
    </div>

        <div class="row">
            <div class="rTable content-nav-tbl" style=" position: relative; top: 50px;    background: #fff;">
                <div class="rTableRow">

                    <?php if (!empty($institutes)): ?>
                    <div class="rTableCell dropdown">

                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" id="filter_institute">All Institutes <span class="caret"></span></a>
                            <ul class="dropdown-menu white inner-scroll">
                                <li><a href="javascript:void(0)" id="filter_institute_all" onclick="filter_institute('all')">All Institutes </a></li>

                                <?php foreach ($institutes as $institute): 
                                    $institute_tooltip  = (strlen($institute['ib_name']) > 15) ? ' title="' . $institute['ib_name'] . '"' : '';
                                    ?>
                                <li><a href="javascript:void(0)" id="filter_institute_<?php echo $institute['id'] ?>" <?php echo $institute_tooltip ?> onclick="filter_institute(<?php echo $institute['id'] ?>)"><?php echo $institute['ib_institute_code'] . ' - ' . $institute['ib_name'] ?></a></li>
                                <?php endforeach;?>

                            </ul>

                    </div>
                    <?php endif;?>

                    <div class="rTableCell">
                        <div class="input-group">
                            <input type="text" class="form-control srch_txt" id="user_keyword" placeholder="Search" />
                            <span id="searchclear" style="display: none;">×</span>
                            <a class="input-group-addon" id="basic-addon2">
                                <i class="icon icon-search"> </i>
                            </a>
                        </div>
                    </div>

                    <div class="rTableCell" >
                        <!-- lecture-control start -->

                        <!-- lecture-control end -->
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- =========================== -->
    <!-- Nav section inside this wrap  --> <!-- END -->


    <!-- LEFT CONTENT --> <!-- STARTS -->
    <!-- ===========================  -->

    <div class='bulder-content-inner' style="position: relative;">


        <div class="col-sm-12  course-cont-wrap" style="background: none;"> <!-- right side bar section -->
            <?php include 'messages.php';?>

        <!-- top Header with drop down and action buttons -->
            <div  style="margin:0 !important">
                <span class="pull-left" style="    padding: 20px 15px;">
                    <!-- <input type="checkbox" class="rdobtn question-option-input" id="selectall">
                    <span class="select-span" id ="sel_all" >Select All</span>
                    <span id="selected_user_count"></span> -->
                    <a href="javascript:void(0)" class="select-all-style no-padding" <?php echo ($total_users < 1)?'style="display:none;"':'' ?> ><label> <input class="user-checkbox-parent " type="checkbox">Select All</label><span id="selected_user_count"></span></a>
                </span>

                <div class="pull-right">
                    <!-- Header left items -->
                    <h4 class="right-top-header user-count">
                        <?php
$user_html = '';
$user_html .= $total_users; //sizeof($users).' / '.$total_users;
$user_html .= ($total_users > 1) ? ' Students' : ' Student';
echo $user_html;
//$remaining_user = $total_users - sizeof($users);
//$remaining_user = ($remaining_user>0)?'('.$remaining_user.')':'';
?>
                    </h4>
                </div>
                <!-- !.Header left items -->
            </div>
            <!-- !.top Header with drop down and action buttons -->
            <div class="table course-cont only-course rTable" style="" id="user_row_wrapper">
                <?php if (!empty($users)): ?>
                    <?php ?>
                <?php else: ?>
                    <div id="popUpMessagePage" class="alert alert-danger">    <a data-dismiss="alert" class="close">x</a>    No Students found.</div>
                <?php endif;?>
            </div>

            <!-- Preivew of  test content will show here -->
            <!-- <div class="preivew-area test-content generate-test-wrapper" id="generate_test_wrapper"> -->

                <div class="row">
                    <div id="pagination_wrapper">
                    </div>
                </div>
        </div>
    <!-- right side bar section -->


    </div>
    <!-- ==========================  -->
    <!--  LEFT CONTENT--> <!-- ENDS -->


</section>


<!-- Basic All Javascript -->
<script type="text/javascript">

    var __limit  = <?php echo $limit; ?>;
    var __users  = atob('<?php echo base64_encode(json_encode($users)); ?>');
    var __offset = Number(<?php echo isset($_GET['offset']) ? $_GET['offset'] : '1' ?>);
    var __course_id     = <?php echo $course_id; ?>;
    var __totalUsers    = <?php echo $total_users; ?>;


    var __userObject = {};
    $(document).ready(function() {
        var users       = {};
        __userObject    = $.parseJSON(__users);
        users.users     = $.parseJSON(__users);
        if (users.users.length > 0) {
            $('#user_row_wrapper').html(renderUserHtml(JSON.stringify(users)));
        } else {
            $('.user-count').html("No Students");
            $('#user_row_wrapper').html(renderPopUpMessagePage('error', 'No Students found.'));
            $('#popUpMessagePage .close').css('display', 'none');
        }
        $('#filter_batch_div').css('display', 'none');
        renderPagination(__offset, __totalUsers);
    });





$(document).on('click', '.user-checkbox', function() {
    var user_id = $(this).val();
    if ($('.user-checkbox:checked').length == $('.user-checkbox').length) {
        $('.user-checkbox-parent').prop('checked', true);
    }
    if ($(this).is(':checked')) {
        $('.list-button').removeClass('list-disabled');
        __user_selected.push(user_id);
    } else {
        $('.user-checkbox-parent').prop('checked', false);
        $('.list-button').addClass('list-disabled');
        removeArrayIndex(__user_selected, user_id);
    }
    if (__user_selected.length > 1) {
        $("#selected_user_count").html(' (' + __user_selected.length + ')');
        $("#user_bulk").css('display', 'block');
    } else {
        $("#selected_user_count").html('');
        $("#user_bulk").css('display', 'none');
    }
console.log(__user_selected);
});

$(document).on('click', '.user-checkbox-parent', function() {
    var parent_check_box = this;
    __user_selected = new Array();
    $('.user-checkbox').not(':disabled').prop('checked', $(parent_check_box).is(':checked'));
    $('.list-button').addClass('list-disabled');
    if ($(parent_check_box).is(':checked') == true) {
        $('.user-checkbox').not(':disabled').each(function(index) {
            __user_selected.push($(this).val());
        });
        $('.list-button').removeClass('list-disabled');
    }
    if (__user_selected.length > 0) {
        $("#selected_user_count").html(' (' + __user_selected.length + ')');
        $("#user_bulk").css('display', 'block');
    } else {
        $("#selected_user_count").html('');
        $("#user_bulk").css('display', 'none');
    }

});

var timeOut = '';
$(document).on('keyup', '#user_keyword', function() {
    clearTimeout(timeOut);
    timeOut = setTimeout(function() {
        __offset = 1;
        getUsers();
    }, 600);
});

$(document).on('click', '#searchclear', function(){
    //__user_selected = new Array();
    $("#user_bulk").css('display', 'none');
    __offset = 1;
    getUsers();
});

$(document).on('click', '#basic-addon2', function() {
    var user_keyword = $('#user_keyword').val().trim();        
    if(user_keyword == '')
    {
        lauch_common_message('KEYWORD MISSING', 'Please enter a valid input to search.');
    }
    else{
        __user_selected = new Array();
        $("#selected_user_count").html('');
        $("#user_bulk").css('display', 'none');
        __offset = 1;
        getUsers();
    }
});

function loadMoreUsers() {
    getUsers();
}

function renderUserHtml(response) {
    // $("#selected_user_count").html('');
    var data = $.parseJSON(response);
    var userHtml = '';
    if (data['users'].length > 0) {
        for (var i = 0; i < data['users'].length; i++) {
            userHtml += '<div class="rTableRow" id="user_row_' + data['users'][i]['user_id'] + '" data-name="' + data['users'][i]['us_name'] + '" data-email="' + data['users'][i]['us_email'] + '">';

            userHtml += renderUserRow(data['users'][i]);

            userHtml += '</div>';
        }
    }
    return userHtml;
}


function renderUserRow(data){
    // var data = $.parseJSON(response);

    var checked = false;

    if(__user_selected.includes(data['user_id']))
    {
        checked = 'checked';
    }

    var userHtml = '';

    if(data)
    {
        userHtml += '    <div class="rTableCell"> ';
        userHtml += '        <input type="checkbox" class="user-checkbox user-selector" value="' + data['user_id'] + '" id="user_details_' + data['user_id'] + '" '+checked+'> ';

        // userHtml += ' <span style="display: inline-block; vertical-align: middle;  margin-right: 10px;"><img class="profile-pic media-object pull-left img-circle" data-name="' + data['us_name'] + '"></span>';
        userHtml += '        <span class="wrap-mail ellipsis-hidden manage-stud-listwrapper"> ';
        userHtml += '            <div class="ellipsis-style">';
        userHtml += '                <a href="javascript:void(0)"><label class="manage-stud-list" for="user_details_' + data['user_id'] + '"><span class="list-user-name">   <svg style="vertical-align: middle; margin: 0px 10px" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Layer_1" x="0px" y="0px" width="16px" height="18px" viewBox="0 0 16 18" enable-background="new 0 0 16 18" fill="#64277d" xml:space="preserve"><g> <path d="M8,1.54v0.5c1.293,0.002,2.339,1.048,2.341,2.343C10.339,5.675,9.293,6.721,8,6.724C6.707,6.721,5.66,5.675,5.657,4.382 C5.66,3.088,6.707,2.042,8,2.04V1.54v-0.5c-1.846,0-3.342,1.496-3.342,3.343c0,1.845,1.497,3.341,3.342,3.341 c1.846,0,3.341-1.496,3.341-3.341C11.341,2.536,9.846,1.04,8,1.04V1.54z"/> <path d="M2.104,16.46c0-1.629,0.659-3.1,1.727-4.168C4.899,11.225,6.37,10.565,8,10.565s3.1,0.659,4.168,1.727 c1.067,1.068,1.727,2.539,1.727,4.168h1c0-3.808-3.087-6.894-6.895-6.895c-3.808,0-6.895,3.087-6.895,6.895H2.104z"/></g></svg>' + data['us_name'] + '</span><span class="list-institute-code">' + data['us_institute_code'] + ' </span><span class="list-register-number" style="display:inline;">' + data['us_phone'] + ' </span> </label></a> <br>';
        userHtml += '            </div>';
        userHtml += '        </span>';
        userHtml += '    </div><div class="rTableCell"> </div> ';
    }

    return userHtml;
}

var __institute_id = '';
var __branch_id = '';

function filter_institute(institute_id) {

    if (institute_id == 'all') {
        __institute_id = '';
        $('#filter_institute').html('All Institutes <span class="caret"></span>');
    } else {
        __institute_id = institute_id;
        $('#filter_institute').html($('#filter_institute_' + institute_id).text().substring( 0, $('#filter_institute_' + institute_id).text().indexOf('-') ) + '<span class="caret"></span>');
    }
    __offset = 1;
    getUsers();
    $("#selected_user_count").html('');
    __user_selected = new Array();
    $("#user_bulk").css('display', 'none');
}

function getUsers() {
    //$('#loadmorebutton').html('Loading..');
    var keyword = $('#user_keyword').val().trim();
    if(__user_selected.length > 1){
        $('#selected_user_count').text(' ('+__user_selected.length+')');
    }
    
    $.ajax({
        url: admin_url + 'course/enroll_users_json',
        type: "POST",
        data: { "is_ajax": true, "course_id":__course_id, "institute_id": __institute_id, "branch_id": __branch_id, "keyword": keyword, 'limit': __limit, 'offset': __offset },
        success: function(response) {
            $('.user-checkbox-parent').prop('checked', false);
            // __user_selected = new Array();

            var data = $.parseJSON(response);
            // //console.log(data);
            var remainingUser = 0;
            //
            renderPagination(__offset, data['total_users']);
            //$('#loadmorebutton').hide();
            if (data['users'].length > 0) {
                __offset++;
                if (__offset == 2) {
                    __totalUsers = data['total_users'];
                    __shownUsers = data['users'].length;
                    remainingUser = (data['total_users'] - data['users'].length);
                    var totalUsersHtml = data['total_users'] + ' ' + ((data['total_users'] == 1) ? "Student" : "Students");//data['users'].length + ' / ' + data['total_users'] + ' ' + ((data['total_users'] == 1) ? "Student" : "Students");
                    scrollToTopOfPage();
                    $('.user-count').html(totalUsersHtml);
                    $('#user_row_wrapper').html(renderUserHtml(response));
                } else {
                    __totalUsers = data['total_users'];
                    __shownUsers = ((__offset - 2) * data['limit']) + data['users'].length;
                    remainingUser = (data['total_users'] - (((__offset - 2) * data['limit']) + data['users'].length));
                    var totalUsersHtml = data['total_users'] + ' Students';//(((__offset - 2) * data['limit']) + data['users'].length) + ' / ' + data['total_users'] + ' Students';
                    $('.user-count').html(totalUsersHtml);
                    $('#user_row_wrapper').html(renderUserHtml(response));
                }
            } else {
                $('.user-count').html("No Students");
                $('#user_row_wrapper').html(renderPopUpMessagePage('error', 'No Students found.'));
                $('#popUpMessagePage .close').css('display', 'none');
            }
            
            if(__totalUsers < 2 || data['users'].length < 1) {
                $('.select-all-style').css('display', 'none');
            } else {
                $('.select-all-style').css('display', 'block');
            }
        }
    });
}

var __enrolStudentInProgress = false;
function enrollStudents()
{
    if(__user_selected.length == 0){
        var messageObject = {
            'body': 'Please select atleast one student',
            'button_yes': 'OK'        
        };
        callback_warning_modal(messageObject);
        return false;
    }
    $.ajax({
        url: admin_url+'course/enroll_students',
        type: "POST",
        data:{"is_ajax":true, "course_id":__course_id, 'user_ids':JSON.stringify(__user_selected)},
        beforeSend: function() { $('#enroll_user_confirmed').attr('disabled', 'disabled'); },
        success: function(response) {
           
            var data = $.parseJSON(response);
            if(data['success'] == true)
            {
                $('#enroll_user_confirmed').removeAttr('disabled');
                location.href = admin_url+'course/users/'+__course_id+'?success';
            } else
            {
                lauch_common_message('Something went Wrong' , data['message']);
            }
            __enrolStudentInProgress = false;
        }
    });
}


//by thnaveer for pagntion
function renderPagination(offset, totalUsers) {
    offset = Number(offset);
    totalUsers = Number(totalUsers);
    var totalPage = Math.ceil(totalUsers / __limit);
    if (offset <= totalPage && totalPage > 1) {
        var paginationHtml = '';
        paginationHtml += '<ul class="pagination pagination-wrapper"  style="left:0px;">';
        paginationHtml +=   generatePagination(offset, totalPage);
        paginationHtml += '</ul>';
        $('#pagination_wrapper').html(paginationHtml);
    } else {
        $('#pagination_wrapper').html('');
    }
}
$(document).on('click', '.locate-page', function(){
    __offset = $(this).attr('data-page');
    getUsers();
});

function clearUserCache() {
    __user_selected = new Array();
    console.log('__user_selected 416');
    __course_selected = new Array();
    $("#selected_user_count").html('');
}

function refreshListing() {
    if($('.pagination li.active a').hasClass('pagination-last-page') == true) {
        if($('.user-listing-row').length == 0) {
            __offset = $('.pagination li.active a').attr('data-page');
            __offset = __offset - 1;
            if(__offset == 0) {
                __offset = 1;
            }
            getUsers();
        }
    } else {
        if($('.user-listing-row').length == 0) {
            __offset = $('.pagination li.active a').attr('data-page');
            getUsers();
        }
    }
}
//end

clearUserCache();
</script>
<!-- END -->

<?php include_once 'footer.php';?>
<!-- Modal pop up contents:: Delete Section popup-->


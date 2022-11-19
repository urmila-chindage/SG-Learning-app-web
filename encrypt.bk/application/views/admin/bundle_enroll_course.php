<?php include_once "header.php";?>

<style>
    .question-bank-bulk{
        padding-bottom: 0px !important;
    }
    .question-count{
        overflow: visible !important;
    }

    iframe{ border:none;}
    .menu-block{display:none} 
    .course-container{ position: static; padding-left: 0px; } 
    .course-container{ position: static; padding-left: 0px; }
    .message-body{padding:10px 0;margin-bottom:10px}
    .icon-align{padding-top: 11px;}

</style>


<!-- SIDEBAR RIGHT SIDE FIXED --> <!-- START -->
<div class="right-wrap base-cont-top container-fluid pull-right right-wrap-height-adjust" style="top:95px!important;">
        <a href="javascript:void(0)" id="enroll_user_confirmed" class="btn btn-big btn-green selected full-width-btn" onclick="enrollCourses()">
            ADD ITEMS 
        </a>
        <a href="<?php echo admin_url('bundle/basic/' . $course_id) ?>" class="btn btn-big btn-blue full-width-btn">
            CANCEL
            <ripples></ripples>
        </a>

    <!--  Adding list group  --> <!-- END  -->
</div>
<!-- SIDEBAR RIGHT SIDE FIXED --> <!-- END -->



<section class="content-wrap" style="top:94px;">

    <!-- Nav section inside this wrap  --> <!-- START -->
    <!-- =========================== -->
<div class="container-fluid nav-content nav-course-content add-question-block" style="width:100% !important;top: 0px !important;height: 50px;">
    <div class="col-sm-12 bottom-line question-head">
        <h3 class="question-title">Add Items to bundle</h3>
        <span class="cb-close-qstn" style="right: 30px;"><i class="icon icon-cancel-1" onclick="location.href='<?php echo admin_url('bundle/basic/' . $course_id) ?>'"></i></span>
    </div>

        <div class="row">
            <div class="rTable content-nav-tbl" style=" position: relative; top: 50px;    background: #fff;">
                <div class="rTableRow">
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
$user_html .= ($total_users > 1) ? ' Items' : ' Item';
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
                    <div id="popUpMessagePage" class="alert alert-danger">    <a data-dismiss="alert" class="close">x</a>    No Items found.</div>
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
    var __bundle_id     = <?php echo $course_id; ?>;
    var __totalUsers    = <?php echo $total_users; ?>;


    var __userObject = {};
    $(document).ready(function() {
        var users       = {};
        __userObject    = $.parseJSON(__users);
        users.users     = $.parseJSON(__users);
        if (users.users.length > 0) {
            $('#user_row_wrapper').html(renderUserHtml(JSON.stringify(users)));
        } else {
            $('.user-count').html("No Items");
            $('#user_row_wrapper').html(renderPopUpMessagePage('error', 'No Items found.'));
            $('#popUpMessagePage .close').css('display', 'none');
        }
        $('#filter_batch_div').css('display', 'none');
        renderPagination(__offset, __totalUsers);
    });



var __course_selected = new Array();

$(document).on('click', '.user-checkbox', function() {
    var user_id = $(this).val();
    if ($('.user-checkbox:checked').length == $('.user-checkbox').length) {
        $('.user-checkbox-parent').prop('checked', true);
    }
    if ($(this).is(':checked')) {
        $('.list-button').removeClass('list-disabled');
        __course_selected.push(user_id);
    } else {
        $('.user-checkbox-parent').prop('checked', false);
        $('.list-button').addClass('list-disabled');
        removeArrayIndex(__course_selected, user_id);
    }
    if (__course_selected.length > 1) {
        $("#selected_user_count").html(' (' + __course_selected.length + ')');
        $("#user_bulk").css('display', 'block');
    } else {
        $("#selected_user_count").html('');
        $("#user_bulk").css('display', 'none');
    }

});

$(document).on('click', '.user-checkbox-parent', function() {
    var parent_check_box = this;
    __course_selected = new Array();
    $('.user-checkbox').not(':disabled').prop('checked', $(parent_check_box).is(':checked'));
    $('.list-button').addClass('list-disabled');
    if ($(parent_check_box).is(':checked') == true) {
        $('.user-checkbox').not(':disabled').each(function(index) {
            __course_selected.push($(this).val());
        });
        $('.list-button').removeClass('list-disabled');
    }
    if (__course_selected.length > 0) {
        $("#selected_user_count").html(' (' + __course_selected.length + ')');
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
    __course_selected = new Array();
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
        __course_selected = new Array();
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
        userHtml += '<div class="rTableRow d-flex align-center">';
        userHtml += '<div class="rTableCell" style="width:210px"><span class="wrap-mail ellipsis-hidden"><div class="ellipsis-style fixed-col"><a href="javascript:void(0)">';
        userHtml += '<strong>Item Code</strong></a></div></span></div>';
        userHtml += '<div class="rTableCell" style="width:210px"><span class="wrap-mail ellipsis-hidden"><div class="ellipsis-style fixed-col"><a href="javascript:void(0)">';
        userHtml += '<strong>Item Name</strong></a></div></span></div>';
        userHtml += '<div class="rTableCell" style="width:210px"><div class="ellipsis-style fixed-col"><label><span>';
        userHtml += '<strong>Item Price</strong></span></label></div></div>';
        userHtml += '<div class="rTableCell" style="width:210px"><span class="wrap-mail ellipsis-hidden"><div class="ellipsis-style"><a href="javascript:void(0)">';
        userHtml += '<strong>Status</strong></a></div></span></div>';
        userHtml += '<div class="rTableCell pad0" style="width:180px">';
        userHtml += '<span><strong>Access</strong></span></div></div>';
        for (var i = 0; i < data['users'].length; i++) {
            userHtml += '<div class="rTableRow" id="user_row_' + data['users'][i]['id'] + '" >';

            userHtml += renderUserRow(data['users'][i]);

            userHtml += '</div>';
        }
    }
    return userHtml;
}

function renderUserRow(data){
    // var data = $.parseJSON(response);
    var coursePrice = '';
    var userHtml    = '';
    if(data)
    {
        
        userHtml += '    <label class="d-flex"> ';
        userHtml += '    <div class="rTableCell" style="white-space: nowrap;width:210px;"> ';
        userHtml += '        <input type="checkbox" class="user-checkbox" value="' + data['id'] + '" id="user_details_' + data['id'] + '"> ';

        // userHtml += ' <span style="display: inline-block; vertical-align: middle;  margin-right: 10px;"><img class="profile-pic media-object pull-left img-circle" data-name="' + data['us_name'] + '"></span>';
        userHtml += '        <span class="wrap-mail ellipsis-hidden manage-stud-listwrapper"> ';
        userHtml += '            <div class="ellipsis-style">';
        userHtml += '                <a href="javascript:void(0)"><label class="manage-stud-list" for="user_details_' + data['id'] + '"><span class="list-user-name">';
        userHtml += '                <svg fill="#64277d" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Layer_1" x="0px" y="0px" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;width: 19px;" xml:space="preserve">';
        userHtml += '                <g><g><path d="M506.103,188.455L260.77,65.788c-3.003-1.501-6.538-1.501-9.54,0L5.897,188.455C2.283,190.262,0,193.956,0,197.995    c0,4.041,2.283,7.734,5.897,9.54l81.215,40.607v81.514c0,50.342,74.185,89.778,168.889,89.778S424.889,380,424.889,329.656    v-81.514l28.346-14.173v77.795c-13.067,4.453-22.494,16.838-22.494,31.393c0,13.196,7.748,24.612,18.932,29.949l-22.659,59.783    c-1.242,3.278-0.795,6.957,1.197,9.841s5.273,4.606,8.778,4.606h53.833c3.505,0,6.786-1.722,8.779-4.606    c1.991-2.884,2.438-6.563,1.197-9.84l-22.659-59.783c11.182-5.337,18.93-16.753,18.93-29.949c0-14.556-9.43-26.942-22.499-31.394    v-88.461l31.536-15.767c3.614-1.807,5.896-5.501,5.896-9.54C512,193.956,509.717,190.262,506.103,188.455z M363.109,376.057    c-28.431,14.215-66.47,22.045-107.109,22.045c-40.639,0-78.677-7.829-107.109-22.045c-25.703-12.852-40.446-29.764-40.446-46.4    V258.81l142.785,71.393c1.502,0.751,3.136,1.126,4.77,1.126s3.268-0.375,4.77-1.126l142.785-71.393v70.847h0    C403.555,346.293,388.813,363.205,363.109,376.057z M452.437,426.005l11.467-30.253l11.466,30.253H452.437z M475.732,343.159    c0.001,6.524-5.306,11.83-11.828,11.83c-6.524,0-11.83-5.307-11.83-11.83c0-6.506,5.279-11.798,11.777-11.827    c0.017,0,0.033,0.002,0.05,0.002c0.018,0,0.035-0.002,0.052-0.002C470.452,331.36,475.732,336.652,475.732,343.159z     M461.736,205.868l-204.78-18.492c-5.867-0.534-11.053,3.797-11.583,9.664c-0.53,5.868,3.797,11.053,9.664,11.583l170.412,15.388    L256,308.737L34.518,197.995L256,87.255l221.482,110.741L461.736,205.868z"/>';
        userHtml += '                </g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g></svg><span style="vertical-align: super;margin-left: 5px;">' + data['cb_code'] + '<span></span> </label></a> <br>';
        userHtml += '            </div>';
        userHtml += '        </span>';
        userHtml += '    </div>';
        userHtml += '    <div class="rTableCell" style="width:210px;"> <div class="ellipsis-style">'+data['cb_title']+'</div></div> ';
        if(data['cb_is_free'] == '1'){
            coursePrice = 'Free';
        }else{
            coursePrice = (data['cb_discount']>0)?data['cb_discount']:data['cb_price'];
            coursePrice = '&#8377; '+ coursePrice ;
        }
        userHtml += '    <div class="rTableCell" style="width:210px;"> <div class="ellipsis-style">'+coursePrice+'</div></div> ';
        var courseStatus = (data['cb_status']==1)?'public':'private';
        userHtml += '    <div class="rTableCell" style="width:210px;"> <div class="ellipsis-style">'+courseStatus+'</div></div> ';
        var courseValidity = '';
        if(data['cb_access_validity'] == 0){
            courseValidity = '<span class="text-green">Unlimited</span>';
        }else if(data['cb_access_validity'] == 1){
            courseValidity = '<span class="text-green">'+data['cb_validity']+" days</span>";
        }else if(data['cb_access_validity'] == 2){

            var currentDate     = new Date();
            currentDate.setHours(0,0,0,0);
            var existingDate    = new Date(data['cb_validity_date']);
            existingDate.setHours(0,0,0,0);
            if(currentDate > existingDate){
                courseValidity = '<span class="text-red">Expired</span>';
            }else if(currentDate == existingDate){
                courseValidity = '<span class="text-orange">Today</span>';
            }else{
                var day     = existingDate.getDate();
                var month   = existingDate.getMonth()+1; 
                var year    = existingDate.getFullYear();
                if(day < 10) 
                {
                    day     ='0'+ day;
                } 

                if(month < 10) 
                {
                    month   ='0'+ month;
                } 
                validityDate = day+'/'+month+'/'+year;
                courseValidity ='<span class="text-green">'+validityDate+'</span>';
            }
        }
        userHtml += '    <div class="rTableCell" style="width:180px;"> <div class="ellipsis-style">'+courseValidity+'</div></div> </label> ';

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
    __course_selected = new Array();
    $("#user_bulk").css('display', 'none');
}

function filter_branch(branch_id) {
    if (branch_id == 'all') {
        __branch_id = '';
        $('#filter_branch').html('All Branches <span class="caret"></span>');
    } else {
        __branch_id = branch_id;
        $('#filter_branch').html($('#filter_branch_' + branch_id).text().substring( 0, $('#filter_branch_' + branch_id).text().indexOf('-') ) + '<span class="caret"></span>');
    }
    __offset = 1;
    getUsers();
    $("#selected_user_count").html('');
    __course_selected = new Array();
    $("#user_bulk").css('display', 'none');
}

function getUsers() {
    //$('#loadmorebutton').html('Loading..');
    var keyword = $('#user_keyword').val().trim();

    $.ajax({
        url: admin_url + 'bundle/enroll_course_json',
        type: "POST",
        data: { "is_ajax": true, "course_id":__bundle_id, "institute_id": __institute_id, "branch_id": __branch_id, "keyword": keyword, 'limit': __limit, 'offset': __offset },
        success: function(response) {
            $('.user-checkbox-parent').prop('checked', false);
            // __course_selected = new Array();

            var data = $.parseJSON(response);
            var remainingUser = 0;
            clearUserCache();
            renderPagination(__offset, data['total_users']);
            //$('#loadmorebutton').hide();
            if (data['users'].length > 0) {
                __offset++;
                if (__offset == 2) {
                    __totalUsers = data['total_users'];
                    __shownUsers = data['users'].length;
                    remainingUser = (data['total_users'] - data['users'].length);
                    var totalUsersHtml = data['total_users'] + ' ' + ((data['total_users'] == 1) ? "Item" : "Items");//data['users'].length + ' / ' + data['total_users'] + ' ' + ((data['total_users'] == 1) ? "Student" : "Students");
                    scrollToTopOfPage();
                    $('.user-count').html(totalUsersHtml);
                    $('#user_row_wrapper').html(renderUserHtml(response));
                } else {
                    __totalUsers = data['total_users'];
                    __shownUsers = ((__offset - 2) * data['limit']) + data['users'].length;
                    remainingUser = (data['total_users'] - (((__offset - 2) * data['limit']) + data['users'].length));
                    var totalUsersHtml = data['total_users'] + ' Items';//(((__offset - 2) * data['limit']) + data['users'].length) + ' / ' + data['total_users'] + ' Students';
                    $('.user-count').html(totalUsersHtml);
                    $('#user_row_wrapper').html(renderUserHtml(response));
                }
            } else {
                $('.user-count').html("No Items");
                $('#user_row_wrapper').html(renderPopUpMessagePage('error', 'No Items found.'));
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
function enrollCourses()
{
    if(__course_selected.length == 0){
        var messageObject = {
            'body': 'Please select atleast one item',
            'button_yes': 'OK'        
        };
        callback_warning_modal(messageObject);
        return false;
    }else{
        $.ajax({
            url: admin_url+'bundle/check_course_valid',
            type: "POST",
            data:{"is_ajax":true,'course_ids':JSON.stringify(__course_selected)},
            beforeSend: function() { $('#enroll_user_confirmed').attr('disabled', 'disabled'); },
            success: function(response) {
                $('#enroll_user_confirmed').removeAttr('disabled');
                var data        = $.parseJSON(response);
                if(data['error'] == true){
                    // if(data['active_course_list'].length > 0)
                    // {
                        var course_list     = (data['course_list']!='')?data['course_list'].split(","):'';
                        var messageHtml     = '<div id="message_title" style="font-weight: normal;margin-bottom:10px"><div style="padding: 15px 0px;">Below items do not satisfy activation criteria</div>';
                        messageHtml     += '<ol style="padding: 0px;color: #757575;">';
                        
                        var sl = 1;
                        $.each( course_list, function( key, value ) {
                            messageHtml +='<li><h4>'+sl+'. <b>'+value+'</b></h4></li>';
                            sl++;
                        });
                        messageHtml     += '</ol>';
                        messageHtml     += '<div style="display: flex;justify-content: center;color: #f78834;font-size: 13px;padding-top: 13px;"><div><p>Note:</div> <div class="text-left" style="text-align: left;padding-left: 15px;font-size: 13px;">* refer <b>OVERVIEW</b> of these items to make public.</p>';
                        if(data['active_course_list'].length > 0){
                            messageHtml     += '<p style="padding-top: 20px;font-size: 17px;font-weight: 600;">Do you want to add all other activated items to the bundle.</p></div>';
                            var messageObject   = {
                                'body': messageHtml,
                                'button_yes': 'OK',
                                'button_no': 'CANCEL',
                                'continue_params': {
                                    "bundle_id": __bundle_id,
                                    "course_ids": data['active_course_list']
                                }
                            };
                            callback_warning_modal(messageObject, enrollCoursesConfirmed);
                        }else{
                            lauch_common_message('Something went Wrong' , messageHtml);
                        }
                        
                       
                }else{
                    params                  = {'data':{'bundle_id':__bundle_id,'course_ids':data['active_course_list']}};
                    enrollCoursesConfirmed(params);
                }
                
            }
        });
    }
    
}
function enrollCoursesConfirmed(params)
{
    var bundleId    = params.data.bundle_id;
    var course_ids  = params.data.course_ids;
    $.ajax({
        url: admin_url+'bundle/save_courses',
        type: "POST",
        data:{"is_ajax":true, "bundle_id":bundleId, 'course_ids':JSON.stringify(course_ids)},
        beforeSend: function() { $('#enroll_user_confirmed').attr('disabled', 'disabled'); },
        success: function(response) {
           
            var data = $.parseJSON(response);
            if(data['success'] == true)
            {
                $('#enroll_user_confirmed').removeAttr('disabled');
                location.href = admin_url+'bundle/basic/'+__bundle_id+'?success';
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
    __course_selected = new Array();
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
</script>
<!-- END -->

<?php include_once 'footer.php';?>
<!-- Modal pop up contents:: Delete Section popup-->


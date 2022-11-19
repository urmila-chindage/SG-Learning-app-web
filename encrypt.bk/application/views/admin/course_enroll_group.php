<?php include_once "header.php";?>

<style>
    .question-bank-bulk{
        padding-bottom: 0px !important;
    }
    .question-count{
        overflow: visible !important;
    }
    iframe{ border:none;}
    .batch-row-list .rTableCell{padding-top: 0px !important;padding-bottom: 0px !important;}
    .batch-row-list .rTableCell label{display: block;padding: 10px 0px;}
    .batch-row-list .rTableCell .batch-list{font-size: 14px;color: #444}
</style>



<!-- SIDEBAR RIGHT SIDE FIXED --> <!-- START -->
<div class="right-wrap base-cont-top container-fluid pull-right" style="top:95px !important;height: calc( 100% - 95px);">

        <a href="javascript:void(0)" id="enroll_user_confirmed" class="btn btn-big btn-green selected full-width-btn" onclick="enrollGroups()">
            Enroll Batches
        </a>
        <a href="<?php echo admin_url('course/groups/' . $course_id) ?>" class="btn btn-big btn-blue full-width-btn">
            Cancel
            <ripples></ripples>
        </a>

    <!--  Adding list group  --> <!-- END  -->
</div>
<!-- SIDEBAR RIGHT SIDE FIXED --> <!-- END -->



<section class="content-wrap base-cont-top">

    <!-- Nav section inside this wrap  --> <!-- START -->
    <!-- =========================== -->
<div class="container-fluid nav-content nav-course-content add-question-block" style="width:100% !important;top: 0px !important;height: 50px;">
    <div class="col-sm-12 bottom-line question-head">
        <h3 class="question-title">Enroll Batches</h3>
        <span class="cb-close-qstn" style="right: 30px;"><i class="icon icon-cancel-1" onclick="location.href='<?php echo admin_url('course/groups/' . $course_id) ?>'"></i></span>
    </div>

        <div class="row">
            <div class="rTable content-nav-tbl" style=" position: relative; top: 50px;    background: #fff;">
                <div class="rTableRow">

                    <?php if (!empty($institutes)): ?>
                    <div class="rTableCell dropdown">

                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" id="filter_institute">All Institutes <span class="caret"></span></a>
                            <ul class="dropdown-menu white inner-scroll">
                                <li><a href="javascript:void(0)" id="filter_institute_all" onclick="filter_institute('all')">All Institutes </a></li>

                                <?php foreach ($institutes as $institute): ?>
                                <li><a href="javascript:void(0)" id="filter_institute_<?php echo $institute['id'] ?>" onclick="filter_institute(<?php echo $institute['id'] ?>)"><?php echo $institute['ib_institute_code'] . ' - ' . $institute['ib_name'] ?></a></li>
                                <?php endforeach;?>

                            </ul>

                    </div>
                    <?php endif;?>

                    <div class="rTableCell">
                        <div class="input-group">
                            <input type="text" class="form-control srch_txt" id="group_keyword" placeholder="Search" />
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

    <div class='bulder-content-inner' style="position: relative;top:-20px;">


        <div class="col-sm-12  course-cont-wrap" style="background: none;"> <!-- right side bar section -->
            <?php include 'messages.php';?>

        <!-- top Header with drop down and action buttons -->
            <div  style="margin:0 !important">
                <span class="pull-left" style="    padding: 20px 15px;">
                    <!-- <input type="checkbox" class="rdobtn question-option-input" id="selectall">
                    <span class="select-span" id ="sel_all" >Select All</span>
                    <span id="selected_user_count"></span> -->
                    <a href="javascript:void(0)" class="select-all-style no-padding"><label> <input class="user-checkbox-parent " type="checkbox">Select All</label><span id="selected_user_count"></span></a>
                </span>

                <div class="pull-right">
                    <!-- Header left items -->
                    <h4 class="right-top-header group-count">
                        <?php
$group_html = '';
$group_html .= sizeof($groups) . ' / ' . $total_groups;
$group_html .= ($total_groups > 1) ? ' Batches' : ' Batch';
echo $group_html;
$remaining_groups = $total_groups - sizeof($groups);
$remaining_groups = ($remaining_groups > 0) ? '(' . $remaining_groups . ')' : '';
?>
                    </h4>
                </div>
                <!-- !.Header left items -->
            </div>
            <!-- !.top Header with drop down and action buttons -->
            <div class="table course-cont only-course rTable" style="" id="group_row_wrapper">
                <?php if (!empty($groups)): ?>
                    <?php ?>
                <?php else: ?>
                    <div id="popUpMessagePage" class="alert alert-danger">    <a data-dismiss="alert" class="close">x</a>    No Batches found.</div>
                <?php endif;?>
            </div>

            <!-- Preivew of  test content will show here -->
            <!-- <div class="preivew-area test-content generate-test-wrapper" id="generate_test_wrapper"> -->


        </div>
    <!-- right side bar section -->
    <div class="row">
        <div class="col-sm-12 text-center">
            <a id="loadmorebutton" <?php echo ((!$show_load_button) ? 'style="display:none;"' : '') ?> class="btn btn-green selected" onclick="loadMoreGroups()">Load More <?php echo $remaining_groups ?></a>
        </div>
    </div>
    </div>
    <!-- ==========================  -->
    <!--  LEFT CONTENT--> <!-- ENDS -->


</section>


<!-- Basic All Javascript -->
<script type="text/javascript">

    var __limit         = <?php echo $limit; ?>;
    var __groups        = atob('<?php echo base64_encode(json_encode($groups)); ?>');
    var __offset        = 2;
    var __course_id     = <?php echo $course_id; ?>

    var __groupObject = {};
    $(document).ready(function() {
        var groups       = {};
        __groupObject    = $.parseJSON(__groups);
        groups.groups     = $.parseJSON(__groups);
        if (groups.groups.length > 0) {
            $('#group_row_wrapper').html(renderGroupHtml(JSON.stringify(groups)));
        } else {
            $('.group-count').html("No Batches");
            $('#group_row_wrapper').html(renderPopUpMessagePage('error', 'No Batches found.'));
            $('#popUpMessagePage .close').css('display', 'none');
        }
        // $('#filter_batch_div').css('display', 'none');

    });

var __group_selected = new Array();

$(document).on('click', '.user-checkbox', function() {
    var user_id = $(this).val();
    if ($('.user-checkbox:checked').length == $('.user-checkbox').length) {
        $('.user-checkbox-parent').prop('checked', true);
    }
    if ($(this).is(':checked')) {
        $('.list-button').removeClass('list-disabled');
        __group_selected.push(user_id);
    } else {
        $('.user-checkbox-parent').prop('checked', false);
        $('.list-button').addClass('list-disabled');
        removeArrayIndex(__group_selected, user_id);
    }
    if (__group_selected.length > 1) {
        $("#selected_user_count").html(' (' + __group_selected.length + ')');
        $("#user_bulk").css('display', 'block');
    } else {
        $("#selected_user_count").html('');
        $("#user_bulk").css('display', 'none');
    }
});

$(document).on('click', '.user-checkbox-parent', function() {
    var parent_check_box = this;
    __group_selected = new Array();
    $('.user-checkbox').not(':disabled').prop('checked', $(parent_check_box).is(':checked'));
    $('.list-button').addClass('list-disabled');
    if ($(parent_check_box).is(':checked') == true) {
        $('.user-checkbox').not(':disabled').each(function(index) {
            __group_selected.push($(this).val());
        });
        $('.list-button').removeClass('list-disabled');
    }
    if (__group_selected.length > 0) {
        $("#selected_user_count").html(' (' + __group_selected.length + ')');
        $("#user_bulk").css('display', 'block');
    } else {
        $("#selected_user_count").html('');
        $("#user_bulk").css('display', 'none');
    }

});

var timeOut = '';
$(document).on('keyup', '#group_keyword', function() {
    clearTimeout(timeOut);
    timeOut = setTimeout(function() {
        __offset = 1;
        getGroups();
    }, 600);
});

$(document).on('click', '#searchclear', function(){
    __group_selected = new Array();
    $("#user_bulk").css('display', 'none');
    __offset = 1;
    getGroups();
});

$(document).on('click', '#basic-addon2', function() {
    var group_keyword = $('#group_keyword').val().trim();        
    if(group_keyword == '')
    {
        lauch_common_message('KEYWORD MISSING', 'Please enter a valid input to search.');
    }
    else{
        __group_selected = new Array();
        $("#selected_user_count").html('');
        $("#user_bulk").css('display', 'none');
        __offset = 1;
        getGroups();
    }
});

function loadMoreGroups() {
    getGroups();
}

function renderGroupHtml(response) {
    // $("#selected_user_count").html('');
    var data = $.parseJSON(response);
    var userHtml = '';
    if (data['groups'].length > 0) {
        for (var i = 0; i < data['groups'].length; i++) {
            userHtml += '<div class="rTableRow batch-row-list" id="user_row_' + data['groups'][i]['id'] + '" data-name="' + data['groups'][i]['gp_name'] + '">';

            userHtml += renderGroupRow(data['groups'][i]);

            userHtml += '</div>';
        }
    }
    return userHtml;
}

function renderGroupRow(data){
    // var data = $.parseJSON(response);
    var userHtml = '';
    if(data)
    {
        userHtml += '    <div class="rTableCell"> ';
        userHtml += '        <label><input type="checkbox" class="user-checkbox" value="' + data['id'] + '" id="user_details_' + data['id'] + '">  <svg style="vertical-align: middle; margin: 0px 10px" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Layer_1" x="0px" y="0px" width="26px" height="18px" viewBox="0 0 26 18" enable-background="new 0 0 26 18" fill="#64277d" xml:space="preserve"><g> <path d="M15.73,4.37h-0.5c0.005,0.626-0.278,1.617-0.73,2.384c-0.225,0.386-0.489,0.718-0.755,0.936 C13.477,7.911,13.228,8.01,13,8.01c-0.227,0-0.476-0.099-0.744-0.321c-0.4-0.328-0.791-0.917-1.061-1.55 c-0.273-0.63-0.428-1.311-0.425-1.779h-0.5l0.5,0.02c0.048-1.204,1.036-2.142,2.229-2.142L13.09,2.24h0h0 c1.16,0.048,2.093,0.981,2.142,2.142l0.5-0.021h-0.5v0.01H15.73h0.5V4.36V4.349V4.338c-0.072-1.68-1.419-3.027-3.1-3.098h0h0 l-0.131-0.003c-1.727,0-3.159,1.36-3.228,3.102l0,0.01v0.01c0.005,0.884,0.332,1.965,0.867,2.893 c0.27,0.462,0.592,0.883,0.98,1.206C12.002,8.779,12.472,9.009,13,9.01c0.527,0,0.997-0.229,1.382-0.549 c0.581-0.483,1.023-1.184,1.342-1.919c0.315-0.738,0.505-1.506,0.507-2.171H15.73z"></path> <path d="M7.87,16.2c0-1.419,0.573-2.698,1.502-3.628c0.93-0.929,2.209-1.502,3.627-1.502c1.419,0,2.698,0.573,3.628,1.502 c0.929,0.93,1.502,2.209,1.502,3.628h1c0-3.387-2.743-6.13-6.13-6.13c-3.386,0-6.13,2.743-6.13,6.13H7.87z"></path> <path d="M22.55,6.18h-0.5c0.005,0.512-0.231,1.36-0.609,2.012c-0.187,0.328-0.407,0.609-0.622,0.79 C20.6,9.166,20.406,9.241,20.24,9.24c-0.165,0-0.358-0.075-0.577-0.26c-0.326-0.273-0.654-0.777-0.879-1.315 C18.556,7.13,18.428,6.554,18.43,6.18c0.001-1.005,0.805-1.809,1.811-1.81c1.006,0,1.817,0.808,1.819,1.81h0.5v-0.5h-0.01h-0.5v0.5 H22.55v0.5h0.01h0.5v-0.5c0-1.558-1.267-2.807-2.819-2.81c-0.775,0-1.481,0.313-1.989,0.821C17.743,4.698,17.43,5.405,17.43,6.18 c0.006,0.759,0.284,1.689,0.741,2.5c0.23,0.403,0.507,0.774,0.844,1.062c0.335,0.285,0.751,0.497,1.226,0.498 c0.474-0.001,0.889-0.211,1.224-0.495c0.504-0.43,0.881-1.042,1.153-1.682c0.269-0.642,0.431-1.306,0.433-1.883H22.55v0.5V6.18z"></path> <path d="M17.662,12.858c0.771-0.584,1.672-0.863,2.57-0.864c1.294,0.001,2.569,0.583,3.41,1.688l-0.002-0.002l-0.001-0.002 c0.556,0.745,0.86,1.65,0.86,2.581h1c0-1.149-0.375-2.264-1.06-3.179l-0.001-0.002l-0.002-0.002 c-1.036-1.362-2.613-2.083-4.205-2.083c-1.106,0-2.225,0.349-3.174,1.067L17.662,12.858z"></path> <path d="M3.45,6.18h-0.5c0.005,0.768,0.284,1.7,0.741,2.508C3.922,9.09,4.199,9.459,4.536,9.746 c0.335,0.284,0.75,0.494,1.224,0.495c0.475-0.001,0.891-0.213,1.225-0.499c0.504-0.433,0.88-1.047,1.152-1.687 C8.406,7.412,8.567,6.751,8.57,6.18c0-0.775-0.313-1.482-0.821-1.989C7.242,3.684,6.535,3.37,5.76,3.37 C4.207,3.373,2.94,4.622,2.94,6.18v0.5h0.51V6.18h-0.5H3.45v-0.5H3.44v0.5h0.5c0.002-1.002,0.813-1.81,1.82-1.81 c1.006,0.001,1.809,0.804,1.81,1.81C7.575,6.681,7.339,7.531,6.96,8.185C6.773,8.514,6.553,8.798,6.337,8.98 C6.118,9.166,5.925,9.241,5.76,9.24c-0.167,0-0.36-0.074-0.578-0.258C4.856,8.711,4.528,8.211,4.304,7.674 C4.076,7.14,3.948,6.563,3.95,6.18v-0.5h-0.5V6.18z"></path> <path d="M8.942,12.062c-0.949-0.719-2.068-1.067-3.174-1.067c-1.592,0-3.17,0.721-4.206,2.083l-0.001,0.002L1.56,13.081 C0.875,13.996,0.5,15.11,0.5,16.26h1c0-0.931,0.304-1.836,0.86-2.581l-0.001,0.002l-0.001,0.002 c0.841-1.105,2.116-1.688,3.41-1.688c0.898,0.001,1.799,0.28,2.57,0.864L8.942,12.062z"></path></g></svg>';

        // userHtml += ' <span style="display: inline-block; vertical-align: middle;  margin-right: 10px;"><img class="profile-pic media-object pull-left img-circle" data-name="' + data['us_name'] + '"></span>';
        userHtml += '        <span class="wrap-mail ellipsis-hidden"> ';
        userHtml += '            <div class="ellipsis-style inline"><span class="batch-list">';
        userHtml +=  data['gp_name'];
        userHtml += '            </span></div>';
        userHtml += '        </span></label>';
        userHtml += '    </div><div class="rTableCell"></div>';
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
    getGroups();
    $("#selected_user_count").html('');
    __group_selected = new Array();
    $("#user_bulk").css('display', 'none');
}


function getGroups() {
    $('#loadmorebutton').html('Loading..');
    var keyword = $('#group_keyword').val();

    $.ajax({
        url: admin_url + 'course/enroll_groups_json',
        type: "POST",
        data: { "is_ajax": true, "course_id":__course_id, "institute_id": __institute_id, "keyword": keyword, 'limit': __limit, 'offset': __offset },
        success: function(response) {
            $('.user-checkbox-parent').prop('checked', false);
            // __group_selected = new Array();

            var data = $.parseJSON(response);
            // //console.log(data);
            var remainingUser = 0;
            $('#loadmorebutton').hide();
            if (data['groups'].length > 0) {
                __offset++;
                if (__offset == 2) {
                    __totalUsers = data['total_groups'];
                    __shownUsers = data['groups'].length;
                    remainingUser = (data['total_groups'] - data['groups'].length);
                    var totalUsersHtml = data['groups'].length + ' / ' + data['total_groups'] + ' ' + ((data['total_groups'] == 1) ? "Batch" : "Batches");
                    scrollToTopOfPage();
                    $('.group-count').html(totalUsersHtml);
                    $('#group_row_wrapper').html(renderGroupHtml(response));
                } else {
                    __totalUsers = data['total_groups'];
                    __shownUsers = ((__offset - 2) * data['limit']) + data['groups'].length;
                    remainingUser = (data['total_groups'] - (((__offset - 2) * data['limit']) + data['groups'].length));
                    var totalUsersHtml = (((__offset - 2) * data['limit']) + data['groups'].length) + ' / ' + data['total_groups'] + ' Batches';
                    $('.group-count').html(totalUsersHtml);
                    $('#group_row_wrapper').append(renderGroupHtml(response));
                }
            } else {
                $('.group-count').html("No Batches");
                $('#group_row_wrapper').html(renderPopUpMessagePage('error', 'No Batches found.'));
                $('#popUpMessagePage .close').css('display', 'none');
            }
            if (data['show_load_button'] == true) {
                $('#loadmorebutton').show();
            }

            remainingUser = (remainingUser > 0) ? '(' + remainingUser + ')' : '';
            $('#loadmorebutton').html('Load More ' + remainingUser + '<ripples></ripples>');
        }
    });
}

// var __enrolStudentInProgress = false;
function enrollGroups()
{
    if(__group_selected.length == 0){
        var messageObject = {
            'body': 'Please select atleast one batch',
            'button_yes': 'OK'        
        };
        callback_warning_modal(messageObject);
        return false;
    }
    $.ajax({
        url: admin_url+'course/enroll_group_to_course',
        type: "POST",

        data:{"is_ajax":true, "course_id":__course_id, 'group_ids':JSON.stringify(__group_selected)},
        beforeSend: function() { $('#enroll_user_confirmed').attr('disabled', 'disabled'); },
        success: function(response) {
            
            var data = $.parseJSON(response);
            if(data['success'] == 'true')
            {
                $('#enroll_user_confirmed').removeAttr('disabled');
                location.href = admin_url+'course/groups/'+__course_id+'?success';
                
                
            } else
            {
                var messageObject = {
                    'body': data['message'],
                    'button_yes': 'OK',
                };
                callback_danger_modal(messageObject);
            }
        }
    });
}

</script>
<!-- END -->

<?php include_once 'footer.php';?>
<!-- Modal pop up contents:: Delete Section popup-->


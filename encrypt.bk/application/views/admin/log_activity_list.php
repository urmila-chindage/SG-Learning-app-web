<?php include_once 'report_header.php'; ?>  
<link rel="stylesheet" type="text/css" href="<?php echo assets_url() ?>css/style.css">
<link rel="stylesheet" type="text/css" href="<?php echo assets_url() ?>css/custom.css">
<link rel="stylesheet" href="<?php echo assets_url() ?>css/jquery-ui.css">
<style type="text/css">
    .pagination-wrapper{
        left:0px !important;   
    }
    .left-space{
        margin-left:1.5em;
    }
    .assign-report-nav .nav-content.assessments-content {
        top: 130px;
    }

    .assign-report-nav .nav-content.faculty-nav-content {
        width: calc(100% - 66px);
    }
    .assign-report-nav .nav-content {
        width: calc(50% - 33px);
        position: fixed;
        top: 80px;
    }
    .no-padd-r{padding-right:0 !important;}
    .fullwidth{
        width:100% !important;
    }
    .link-pointer{
        cursor:pointer;
        vertical-align: super;
    }

    .top-reset{top: 50px !important;}
    .settings-top.top90{top:90px !important;}
    body.modal-open{padding:0 !important;overflow:hidden;}
    #filter_batch_div .caret {position: unset !important;right: unset;top: unset;margin: 0;}
    .no-list-message {
        font-size: 24px;
        color: #8d8d8d;
        padding: 30px;
    }
    .question-count{
        display:  block;
        padding: 25px 15px 15px 0px;
        font-size: 16px;
        font-weight: 600;
        color: #6a6a6a;
        text-align:  right;
    }
    #date_clear_start, #date_clear_end{
        position: absolute;
        top: 6px;
        right: 10px;
        z-index: 9;
        font-size: 24px;
        color: #444;
        cursor:pointer;
    }
    .fixed-col{min-width:180px;}
    .activity-msg{padding-left: 0px !important;}
    .ellipsis-style{
        word-break: break-all;
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .activity-msg{
        display: -webkit-box !important;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .rTable.content-nav-tbl{border-collapse: separate !important;}
    .user-type{min-width: 160px;}
</style>


<section class="content-wrap create-group-wrap settings-top top90 reports-left no-padd-r">
    <!-- LEFT CONTENT --> <!-- STARTS -->
    <!-- ===========================  -->

    <div class="container-fluid nav-content nav-course-content add-question-block" style="width:100% !important;top: 0px !important;height: 50px;">
        <div class="col-sm-12 bottom-line question-head">
            <h3 class="question-title">Log Activity List</h3>
            
            <span class="cb-close-qstn" style="right: 30px;"><i class="icon icon-cancel-1" onclick="location.href='<?php echo admin_url('report/course/'); ?>'"></i></span>
        </div> 
    </div>


    <div class="col-sm-12 pad0 assign-report-nav">
        <!-- Nav section inside this wrap  --> <!-- START -->
        <!-- =========================== -->
        <div class="col-sm-12 nav-content faculty-nav-content top-reset assessments-content fullwidth">
            
            
            <div class="row">
                <div class="rTable content-nav-tbl" style="">
                    <div class="rTableRow d-flex">
                        

                        <div class="rTableCell dropdown" id="filter_usertype_div" >
                            <?php if($this->__loggedInUser['role_id'] == '3'): ?>
                            <a href="javascript:void(0)">Tutor<span class="caret"></span></a>
                            <?php else: ?>
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" id="filter_usertype">User Type<span class="caret"></span></a>
                                <ul class="dropdown-menu white inner-scroll" id="usertype_filter_list">
                                <li><a href="javascript:void(0)" id="filter_usertype_all" onclick="filter_usertype('all')">All</a></li>
                                <?php if (!empty($roles)):
                                $role_tooltip = '';
                                ?>
                                <?php foreach ($roles as $role):
                                $role_tooltip = (strlen($role['rl_name']) > 15) ? ' title="' . $role['rl_name'] . '"' : '';
                                ?>
                                <li><a href="javascript:void(0)" id="filter_usertype_<?php echo $role['id'] ?>" <?php echo $role_tooltip; ?> onclick="filter_usertype(<?php echo $role['id'] ?>)"><?php echo $role['rl_name'] ?></a></li>
                                <?php endforeach;?>
                                <?php endif;?>
                                </ul>
                            <?php endif;?>    
                        </div>

                        <!-- <div class="rTableCell" style="width:200px;position:relative;">
                            <div class="input-group">
                                <input id="log_date"  class="form-control date-txt text-center" value="" type="text" autocomplete="off" name="" placeholder="dd-mm-yyyy" readonly="readonly">
                                </div>  <span id="dateclear" style="">×</span>
                        </div> -->

                        <div class="rTableCell pos-relative" style="width:200px;">
                                    <!-- datetimepicker -->
                                <input id="log_date_start" class="log_date form-control" value="" type="text" autocomplete="off" name="" placeholder="Start date" readonly="readonly" style="background:#fff;border: 0px;">
                                    <!-- datetime picker -->
                                <span id="date_clear_start" class="date-clear" style="">×</span>
                            </div>

                        <div class="rTableCell pos-relative" style="width:200px;">
                                    <!-- datetimepicker -->
                                <input id="log_date_end" class="log_date form-control" value="" type="text" autocomplete="off" name="" placeholder="End date" readonly="readonly" style="background:#fff;border: 0px;">
                                    <!-- datetime picker -->
                                <span id="date_clear_end" class="date-clear" style="">×</span>
                        </div>

                        <div class="rTableCell">
                            <div class="input-group">
                                <input class="form-control srch_txt" id="activity_keyword" placeholder="Search by name / phone number / email" type="text">
                                <span id="searchclear">×</span>
                                <a class="input-group-addon" id="faculty_search">
                                    <i class="icon icon-search"> </i>
                                </a>
                            </div> 
                        </div>
                        <div class="rTableCell">
                            <div class="btn-group lecture-control btn-right-align" id="generate_test_bulk_action" style="margin-top: 0px;visibility: hidden;">
                                <span class="dropdown-tigger" data-toggle="dropdown" aria-expanded="false">
                                    <span class="label-text">
                                    Bulk Action  <!-- <span class="icon icon-down-arrow"></span> -->
                                    </span>
                                    <span class="tilder"></span>
                                </span>
                                <ul class="dropdown-menu pull-right" role="menu">
                                    <li><a href="javascript:void(0)" class="list-disabled list-button" onclick="assignFaculty('0')">Assign</a></li>

                                </ul>
                            </div>
                        </div>
                        <!--div class="rTableCell">
                            <div class="save-btn"><button class="pull-right btn btn-green" onclick="exportAssignmentReport();">EXPORT</button></div>
                        </div-->
                    </div>
                </div>
            </div>
        </div>     
        <!-- Group content section  -->
    </div>
    <!-- <div class="col-sm-6 pad0 right-content list-right tp175"> -->
    <div class="">
        <!-- =========================== -->
        <div class="container-fluid right-box list-bx">
            <div class="row">
                <div class="col-sm-12 course-cont-wrap"> 
                <span class="question-count"><!--<span id="activity-count"></span>/--><span id="total-count"></span></span>
                    <div class="table course-cont rTable right-table" style="margin-bottom:90px;" id="activity_detail_wrapper">
                    </div>
                    <div class="row">
                        <div class="col-sm-12 text-center" id="pagination_wrapper">
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <!-- ==========================  -->
    <!--  LEFT CONTENT--> <!-- ENDS -->

    <!-- Modal pop up contents :: Create html -->
<div class="modal" data-backdrop="static" data-keyboard="false" id="assign_faculty" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-small" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" id="close_modal" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon icon-cancel-1"></span></button>
                <h4 class="modal-title" id="myModalLabel">Faculty Assign</h4>
            </div>
            <div class="modal-body">
                <div class="col-sm-12">
                    <div class="form-group">
                        <label>Assign to *:</label>
                        <select class="form-control" id="faculty_selected_assign">
                        <option value="0">Select Faculty</option>
                       
                    </div>
                </div>
                 
            </div>
            <div class="modal-footer">
                 <button type="button" class="btn btn-red" data-dismiss="modal">CANCEL</button>
                 <button type="button" class="btn btn-green" id="save_faculty_assign" >ASSIGN</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal pop up contents :: Create html -->
<!-- assignment report modal starts -->
    <div class="modal" id="grade_report_detail" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="z-index: 9999999;overflow-y:scroll;padding:0px !importrant;">
	    <div class="modal-dialog assignment-report" role="document">
	        <div class="modal-content" id="assignment-report-content">
	           
	        </div>
	    </div>
	</div>
	<!-- assignment report modal ends -->
<?php
$role_list = array();
foreach($roles as $role){
    $role_list[$role['id']] = $role['rl_name'];
}
?>

</section>


<script src="<?php echo assets_url() ?>js/datepicker.js"></script>
<script src="<?php echo assets_url() ?>js/datepicker.en.js"></script>

<script>
    var __activityObject   = new Array;
         __activityObject  = atob('<?php echo base64_encode(json_encode($activities)) ?>'); 
         __activityObject  = $.parseJSON(__activityObject);
    var __usertype         = '<?php echo isset($_GET['usertype']) ? $_GET['usertype'] : '' ?>';
    var __limit            = '<?php echo $limit; ?>';
    var __offset           = Number('<?php echo isset($_GET['offset']) ? $_GET['offset'] : '1' ?>');
    var __totalCounts      = '<?php echo $total_activities; ?>';
    var __totalActivities  = '<?php echo $total_activities; ?>';
    var __keyword          = '<?php echo isset($_GET['keyword']) ? $_GET['keyword'] : '' ?>';
    var __roles            = '<?php echo json_encode($role_list); ?>';
    var __role_object      = JSON.parse(__roles);
    var __log_date_start   = '<?php echo isset($_GET['log_date_start']) ? $_GET['log_date_start'] : '' ?>';
    var __log_date_end     = '<?php echo isset($_GET['log_date_end']) ? $_GET['log_date_end'] : '' ?>';
</script>

<?php include_once 'report_footer.php'; ?>

<script>
    
    $(document).ready(function (e) {
        var keyword      = getQueryStringValue('keyword');
        var usertype     = getQueryStringValue('usertype');
        var log_date_start = getQueryStringValue('log_date_start');
        var log_date_end = getQueryStringValue('log_date_end');
        if (keyword != '') {
            __keyword    = keyword;
            keyword      = keyword.split('-').join(' ');
            $('#activity_keyword').val(keyword);
        }

        if (usertype != '') {
            __usertype = usertype;
            if(usertype=='all'){
                $('#filter_usertype').html($('#filter_usertype_' + usertype).text() + '<span class="caret"></span>');
            } else {
                $('#filter_usertype').html($('#filter_usertype_' + usertype).text() + '<span class="caret"></span>');  
            }
        }
        
        if(log_date_start!=''){
            $("#log_date_start").val(log_date_start);
        }
        if(log_date_end!=''){
            $("#log_date_end").val(log_date_end);
        }
        renderActivityHtml(__activityObject);
        renderPagination(__offset, __totalActivities);
    });

    function formatAMPM(date){
        //$('#cs_end_date').val().split("-").reverse();
        var dateArray       = date.split(" ");
        var dateToPrint     = dateArray[0];
        //console.log(date);
        //console.log(dateToPrint, 'dateToPrint', dateArray[1]);
        var date            = new Date(dateArray[0].split("-").reverse()+' '+dateArray[1]);
        //console.log(dateArray[1]);
        //console.log(date, 'date');
        var hours           = date.getHours();
        var minutes         = date.getMinutes();
        var ampm            = hours >= 12 ? 'pm' : 'am';
        hours               = hours % 12;
        hours               = hours ? hours : 12; // the hour '0' should be '12'
        minutes             = minutes < 10 ? '0'+minutes : minutes;
        var strTime         = hours + ':' + minutes + ' ' + ampm;
        return dateToPrint+' '+strTime;
    }
    function renderActivityHtml(activities)
    {
        var activityHtml  = '';
        var activityLength = Object.keys(__activityObject).length;
        if(activityLength!=0){
            $.each(__activityObject, function(activityKey, activity )
            {   //console.log(activity['la_created_date'], 'la_created_date');
                user_type         = (__role_object[activity['la_usertype']]!=undefined)?__role_object[activity['la_usertype']]:'-';
                activityHtml     += '<div class="rTableRow">';
                activityHtml     += '<div class="rTableCell"><div class="ellipsis-style" title="'+activity['la_user_name']+'">'+activity['la_user_name']+'</div></div>';
                activityHtml     += '<div class="rTableCell"><div class="ellipsis-style" title="'+activity['la_user_email']+'">'+activity['la_user_email']+'</div></div>';
                activityHtml     += '<div class="rTableCell"><div class="ellipsis-style user-type">'+user_type+'</div></div>';
                activityHtml     += '<div class="rTableCell pad0 activity-msg text-left" title="'+activity['la_message']+'">'+activity['la_message']+'</div>';
                activityHtml     += '<div class="rTableCell pad0 text-center" >'+formatAMPM(activity['la_created_date'])+'</div>';
                activityHtml     += '</div>';
            });
            $('#activity_detail_wrapper').html(activity_header()+activityHtml);
        }
        else
        {
            activityHtml += '<div class="no-list-message text-center">No log activities found</div>';
            $('#activity_detail_wrapper').html(activityHtml);
        }
       // $("#activity-count").html(activityLength);
        var total_records        = (__totalActivities==1)?__totalActivities+' record':__totalActivities+' records';
        $("#total-count").html(total_records);
        //return activityHtml;
    }
    function activity_header()
    {
        return '<div class="rTableRow"><div class="rTableCell"><span class="wrap-mail ellipsis-hidden"><div class="ellipsis-style fixed-col"><a href="javascript:void(0)"><strong>Name</strong></a></div></span></div><div class="rTableCell"><span class="wrap-mail ellipsis-hidden"><div class="ellipsis-style fixed-col"><a href="javascript:void(0)"><strong>Email</strong></a></div></span></div><!--<div class="rTableCell"><div class="ellipsis-style fixed-col"><label><span><strong>Phone Number</strong></span></label></div></span></div>--><div class="rTableCell"><span class="wrap-mail ellipsis-hidden"><div class="ellipsis-style"><a href="javascript:void(0)"><strong>User Type</strong></a></div></span></div><div class="rTableCell pad0"><span><strong>Activity Message</strong></span></div><div class="rTableCell pad0 text-center fixed-col"><span><strong>Activity Date and Time</strong></span></div></div>';
    }

    function getActivities(){
        if (history.pushState) {
            var link = window.location.protocol + "//" + window.location.host + window.location.pathname;
            if (__keyword !='' || __offset !='' || __usertype !='' || __log_date_start !='' || __log_date_end !='') {
                link += '?';
            }

            if (__keyword != '') {
                link += '&keyword=' + __keyword;
            }

            if (__usertype != '') {
                link += '&usertype=' + __usertype;
            }

            if(__log_date_start != ''){
                link += '&log_date_start=' + __log_date_start;
            }

            if(__log_date_end != ''){
                link += '&log_date_end=' + __log_date_end;
            }
            
            __offset = (typeof __offset == 'undefined') ? 1 : __offset;
            link += '&offset=' + __offset;
            window.history.pushState({
                path: link
            }, '', link);
        }
        $.ajax({
            url: webConfigs('admin_url')+'report/log_activity_list',
            type: "POST",
            data:{ "is_ajax":true,"keyword":$.trim(__keyword),"usertype":__usertype,"log_date_start":__log_date_start,"log_date_end":__log_date_end,"limit":__limit,"offset":__offset},
            success: function(response) {
                var data  = $.parseJSON(response);
                __totalActivities = data['total_activities'];
                renderPagination(__offset, __totalActivities);
                if(data['error'] == false)
                {
                    if(typeof data['activities'] != 'undefined')
                    {
                        $('#activity_detail_wrapper').html('');
                        __activityObject = data['activities'];
                        renderActivityHtml(__activityObject);
                        renderPagination(__offset, __totalActivities);
                        refreshListing();
                    }
                }
            }
        });
    }

    $(".srch_txt").keyup(function(){
        $("#searchclear").toggle(Boolean($(this).val()));
    });
    $("#searchclear").toggle(Boolean($(".srch_txt").val()));
    $("#searchclear").click(function(){
        $(".srch_txt").val('').focus();
        $(this).hide();
        __keyword = '';
        __offset   = 1;
        getActivities();
    });

    function getQueryStringValue(key) {
        return decodeURIComponent(window.location.search.replace(new RegExp("^(?:.*[&\\?]" + encodeURIComponent(key).replace(/[\.\+\*]/g, "\\$&") + "(?:\\=([^&]*))?)?.*$", "i"), "$1"));
    }

    /* pagination */
    function renderPagination(offset, totalAttempts) {
        offset = Number(offset);
        totalAttempts = Number(totalAttempts);
        if ((totalAttempts > __limit) > 0) {
            var paginationHtml = '';
            paginationHtml += '<ul class="pagination pagination-wrapper">';
            paginationHtml += generatePagination(offset, Math.ceil(totalAttempts / __limit));
            paginationHtml += '</ul>';
            $('#pagination_wrapper').html(paginationHtml);
            // scrollToTopOfPage();
        } else {
            $('#pagination_wrapper').html('');
        }
    }

    $(document).on('click', '.locate-page', function () {
        __offset = $(this).attr('data-page');
        getActivities();
    });

    function refreshListing() {
        if ($('.pagination li.active a').hasClass('pagination-last-page') == true) {
            if ($('.assignment-listing-row').length == 0) {
                __offset = $('.pagination li.active a').attr('data-page');
                __offset = __offset - 1;
                if (__offset == 0) {
                    __offset = 1;
                }
            }
        } else {
            if ($('.assignment-listing-row').length == 0) {
                __offset = $('.pagination li.active a').attr('data-page');
            }
        }
    }

    var timeOut = '';
    $(document).on('keyup', '#activity_keyword', function(){
        __offset   = 1;
        clearTimeout(timeOut);
        timeOut = setTimeout(function(){
            __keyword = $('#activity_keyword').val();
            getActivities();
        }, 600);
    });

    function filter_usertype(role_id) {
        if (role_id == 'all') {
            __usertype = 'all';
            $('#filter_usertype').html('All<span class="caret"></span>');
        } else {
            __usertype = role_id;
            $('#filter_usertype').html($('#filter_usertype_' + role_id).text() + '<span class="caret"></span>');
        }
        __offset = 1;
        getActivities();
    }


    $(document).on('click', '#date_clear_start', function (){
            if($('#log_date_start').val()){
                $('#log_date_start').val('');
                __log_date_start = '';
                __offset = 1;
                getActivities();
            }
        });

        $(document).on('click', '#date_clear_end', function () {
            if($('#log_date_end').val()){
                $('#log_date_end').val('');
                __log_date_end   = '';
                __offset = 1;
                getActivities();
            }
        });

    $('#log_date_start').val('<?php echo $this->input->get('log_date_start') ?>');
    $('#log_date_end').val('<?php echo $this->input->get('log_date_end') ?>');

    $(document).on('click', '#log_date_start', function () {
        $("#log_date_start").datepicker({
                        language: 'en',
                        minDate: false,
                        maxDate: $('#log_date_end').val() ? new Date($('#log_date_end').val()) : false,
                        dateFormat: 'yyyy-mm-dd',
                        autoClose: true,
                        onSelect: function(dateText, inst) { 
                            __log_date_start = $('#log_date_start').val();
                            __log_date_end   = $('#log_date_end').val();
                            __offset = 1;
                            getActivities();
                        }
                    });
    });

    $(document).on('click', '#log_date_end', function () {
        $("#log_date_end").datepicker({
                        language: 'en',
                        minDate: $('#log_date_start').val() ? new Date($('#log_date_start').val()) : false,
                        maxDate: false,
                        dateFormat: 'yyyy-mm-dd',
                        autoClose: true,
                        onSelect: function(dateText, inst) { 
                            __log_date_start = $('#log_date_start').val();
                            __log_date_end   = $('#log_date_end').val();
                            __offset = 1;
                            getActivities();
                        }
                    });
    });

    $(function(){
        var today = new Date();
        $("#log_date_start").datepicker({
            language: 'en',
            minDate: false,
            maxDate: $('#log_date_end').val() ? new Date($('#log_date_end').val()) : false,
            dateFormat: 'yyyy-mm-dd',
            autoClose: true,
            onSelect: function(dateText, inst) { 
                __log_date_start = $('#log_date_start').val();
                __log_date_end   = $('#log_date_end').val();
                __offset = 1;
                getActivities();
            }
        });

    $("#log_date_end").datepicker({
        language: 'en',
        minDate: $('#log_date_start').val() ? new Date($('#log_date_start').val()) : false,
        maxDate: false,
        dateFormat: 'yyyy-mm-dd',
        autoClose: true,
        onSelect: function(dateText, inst) { 
            __log_date_start = $('#log_date_start').val();
            __log_date_end   = $('#log_date_end').val();
            __offset = 1;
            getActivities();
        }
    });
});
</script>
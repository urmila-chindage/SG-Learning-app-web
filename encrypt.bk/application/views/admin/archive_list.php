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
    #end_date_clear{
        position: absolute;
        top: 6px;
        right: 10px;
        z-index: 9;
        font-size: 24px;
        color: #444;
        cursor:pointer;
    }
    #start_date_clear{
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
            <h3 class="question-title">Archive List</h3>
            
            <span class="cb-close-qstn" style="right: 30px;"><i class="icon icon-cancel-1" onclick="location.href='<?php echo admin_url('report/course/'); ?>'"></i></span>
        </div> 
    </div>


    <div class="col-sm-12 pad0 assign-report-nav">
        <!-- Nav section inside this wrap  --> <!-- START -->
        <!-- =========================== -->
        <div class="col-sm-12 nav-content faculty-nav-content top-reset assessments-content fullwidth">
            
            
            <div class="row">
                <div class="rTable content-nav-tbl" style="">
                    <div class="rTableRow">
                        
                        <div class="rTableCell" style="width:200px;position:relative;">
                            <div class="input-group">
                                <!-- datetimepicker -->
                                <input id="cs_start_date"  class="form-control start-date-txt text-center" value="" type="text" autocomplete="off" name="" placeholder="Subscription from" readonly="readonly">
                                <!-- datetime picker -->
                            </div>  <span id="start_date_clear" style="">×</span>

                        </div>
                       
                      

                        <div class="rTableCell" style="width:200px;position:relative;">
                            <div class="input-group">
                                <!-- datetimepicker -->
                                <input id="cs_end_date"  class="form-control end-date-txt text-center" value="" type="text" autocomplete="off" name="" placeholder="Subscription to" readonly="readonly">
                                <!-- datetime picker -->
                            </div>  <span id="end_date_clear" style="">×</span>

                        </div>

                        <div class="rTableCell">
                            <div class="input-group">
                                <input class="form-control srch_txt" id="activity_keyword" placeholder="Search by name, course title or course code" type="text">
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
                        <div class="rTableCell">
                            <div id="export-btn" class="save-btn"><button class="pull-right btn btn-green" onclick="exportArchiveReport();">EXPORT</button></div>
                        </div
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

</section>

<script src="<?php echo assets_url() ?>js/datepicker.js"></script>
<script src="<?php echo assets_url() ?>js/datepicker.en.js"></script>
<!-- <script src="<?php //echo assets_url() ?>js/language_front.js"></script> -->

<script>
    var __archiveObject    = new Array;
        __archiveObject    = atob('<?php echo base64_encode(json_encode($archive_list)) ?>'); 
        __archiveObject    = $.parseJSON(__archiveObject);
    var __usertype         = '<?php echo isset($_GET['usertype']) ? $_GET['usertype'] : '' ?>';
    var __limit            = '<?php echo $limit; ?>';
    var __offset           = Number('<?php echo isset($_GET['offset']) ? $_GET['offset'] : '1' ?>');
    var __totalCounts      = '<?php echo $total_archives; ?>';
    var __totalArchives    = '<?php echo $total_archives; ?>';
    var __keyword          = '<?php echo isset($_GET['keyword']) ? $_GET['keyword'] : '' ?>';
    var __cs_start_date    = '<?php echo isset($_GET['cs_start_date']) ? $_GET['cs_start_date'] : '' ?>';
    var __cs_end_date      = '<?php echo isset($_GET['cs_end_date']) ? $_GET['cs_end_date'] : '' ?>';
</script>

<?php include_once 'report_footer.php'; ?>

<script>
    
    $(document).ready(function (e) {
        var keyword           = getQueryStringValue('keyword');
        var cs_start_date     = getQueryStringValue('cs_start_date');
        var cs_end_date       = getQueryStringValue('cs_end_date');
        if (keyword != '') {
            __keyword    = keyword;
            keyword      = keyword.split('-').join(' ');
            $('#activity_keyword').val(keyword);
        }

        if(cs_start_date!=''){
            $("#cs_start_date").val(cs_start_date);
        }

        if(cs_end_date!=''){
            $("#cs_end_date").val(cs_end_date);
        }

        renderArchiveHtml(__archiveObject);
        renderPagination(__offset, __totalCounts);
    });
    
    function renderArchiveHtml(activities)
    {
        var archiveHtml   = '';
        var archiveLength = Object.keys(__archiveObject).length;
        var cs_start_date = '';
        var cs_end_date   = '';
        var user_name     = '';
        var register_number = '';
        var course_title  = '';
        var course_code   = '';
        if(archiveLength!=0){
            $("#export-btn").show();
            $.each(__archiveObject, function(archiveKey, archive )
            {
                user_name        = (archive['sa_user_name']!=null)?archive['sa_user_name']:'-';
                //register_number  = (archive['sa_user_register_number']!=null)?archive['sa_user_register_number']:'-';
                course_title     = (archive['sa_course_title']!=null)?archive['sa_course_title']:'-';
                course_code      = (archive['sa_course_code']!=null)?archive['sa_course_code']:'-';
                cs_start_date    = (archive['cs_start_date']!=null)?archive['cs_start_date']:'-';
                cs_end_date      = (archive['cs_end_date']!=null)?archive['cs_end_date']:'-';
                archiveHtml     += '<div class="rTableRow">';
                archiveHtml     += '<div class="rTableCell"><div class="ellipsis-style" title="'+user_name+'">'+archive['sa_user_name']+'</div></div>';
                //archiveHtml     += '<div class="rTableCell"><div class="ellipsis-style">'+register_number+'</div></div>';
                archiveHtml     += '<div class="rTableCell"><div class="ellipsis-style user-type">'+course_title+'('+course_code+')</div></div>';
                archiveHtml     += '<div class="rTableCell pad0 activity-msg text-left" >'+cs_start_date+'</div>';
                archiveHtml     += '<div class="rTableCell pad0 text-center" >'+cs_end_date+'</div>';
                archiveHtml     += '</div>';
            });
            $('#activity_detail_wrapper').html(activity_header()+archiveHtml);
        }
        else
        {
            $("#export-btn").hide();
            archiveHtml += '<div class="no-list-message text-center">No archive records found</div>';
            $('#activity_detail_wrapper').html(archiveHtml);
        }
       // $("#activity-count").html(archiveLength);
        var total_records        = (__totalArchives==1)?__totalArchives+' record':__totalArchives+' records';
        $("#total-count").html(total_records);
        //return archiveHtml;
    }
    function activity_header()
    {
        return `<div class="rTableRow">
                    <div class="rTableCell">
                        <span class="wrap-mail ellipsis-hidden">
                            <div class="ellipsis-style fixed-col">
                                <a href="javascript:void(0)">
                                    <strong>Name</strong>
                                </a>
                            </div>
                        </span>
                    </div>
                    <!--<div class="rTableCell">
                        <span class="wrap-mail ellipsis-hidden">
                            <div class="ellipsis-style fixed-col">
                                <label>
                                    <span>
                                        <strong>Phone Number</strong>
                                    </span>
                                </label>
                            </div>
                        </span>
                    </div>-->
                    <div class="rTableCell">
                        <span class="wrap-mail ellipsis-hidden">
                            <div class="ellipsis-style">
                                <a href="javascript:void(0)">
                                    <strong>Course</strong>
                                </a>
                            </div>
                        </span>
                    </div>
                    <div class="rTableCell pad0">
                        <span>
                            <strong>Subscription Start Date</strong>
                        </span>
                    </div>
                    <div class="rTableCell pad0 text-center fixed-col">
                        <span>
                            <strong>Subscription End Date</strong>
                        </span>
                    </div>
                </div>`;
    }

    function getArchives(){
        if (history.pushState) {
            var link = window.location.protocol + "//" + window.location.host + window.location.pathname;
            if (__keyword !='' || __offset !='' || __cs_start_date !='' || __cs_end_date !='') {
                link += '?';
            }

            if (__keyword != '') {
                link += '&keyword=' + __keyword;
            }

            if (__cs_start_date != '') {
                link += '&cs_start_date=' + __cs_start_date;
            }

            if (__cs_end_date != '') {
                link += '&cs_end_date=' + __cs_end_date;
            }
            
            __offset = (typeof __offset == 'undefined') ? 1 : __offset;
            link += '&offset=' + __offset;
            window.history.pushState({
                path: link
            }, '', link);
        }
        $.ajax({
            url: webConfigs('admin_url')+'report/get_archive_list',
            type: "POST",
            data:{ "is_ajax":true,"keyword":$.trim(__keyword),"cs_end_date":__cs_end_date,"cs_start_date":__cs_start_date,"limit":__limit,"offset":__offset},
            success: function(response) {
                var data  = $.parseJSON(response);
                __totalArchives = data['total_archives'];
                renderPagination(__offset, __totalArchives);
                if(data['error'] == false)
                {
                    if(typeof data['archive_list'] != 'undefined')
                    {
                        $('#activity_detail_wrapper').html('');
                        __archiveObject = data['archive_list'];
                        renderArchiveHtml(__archiveObject);
                        renderPagination(__offset, __totalArchives);
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
        getArchives();
    });

   
    $("#end_date_clear").click(function(){
        if(($(".end-date-txt").val())!=''){
            $(".end-date-txt").val('').focus();
            __cs_end_date = '';
            __offset      = 1;
            getArchives();
        }
    });

    $("#start_date_clear").click(function(){
        if(($(".start-date-txt").val())!=''){
            $(".start-date-txt").val('').focus();
            __cs_start_date = '';
            __offset        = 1;
            getArchives();
        }
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
        getArchives();
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
            getArchives();
        }, 600);
    });

    /*$(function(){
        var today = new Date();
        $("#cs_start_date").datepicker({
            language: 'en',
            minDate: false,
            dateFormat: 'yyyy-mm-dd',
            autoClose: true,
            onSelect: function(dateText, inst) {
                __cs_start_date = dateText;
                __offset        = 1;
                getArchives();
            }
        });
    });

    $(function(){
        var today = new Date();
        $("#cs_end_date").datepicker({
            language: 'en',
            minDate: false,
            dateFormat: 'yyyy-mm-dd',
            autoClose: true,
            onSelect: function(dateText, inst) {
                __cs_end_date = dateText;
                __offset      = 1;
                getArchives();
            }
        });
    });*/


/** date picker starts, please dont remove the below code **/
$('#cs_end_date').val('<?php echo $this->input->get('cs_end_date') ?>');
$('#cs_start_date').val('<?php echo $this->input->get('cs_start_date') ?>');

$(document).on('click', '#cs_start_date', function () {

    $("#cs_start_date").datepicker({
                        language: 'en',
                        minDate: false,
                        maxDate: $('#cs_end_date').val() ? new Date($('#cs_end_date').val().split("-").reverse()) : false,
                        dateFormat: 'dd-mm-yyyy',
                        autoClose: true,
                        onSelect: function(dateText, inst) { 
                            __cs_start_date = $('#cs_start_date').val();
                            __cs_end_date   = $('#cs_end_date').val();
                            __offset = 1;
                            getArchives();
                        }
                    });
});

$(document).on('click', '#cs_end_date', function () {
    
    $("#cs_end_date").datepicker({
                        language: 'en',
                        minDate: $('#cs_start_date').val() ? new Date($('#cs_start_date').val().split("-").reverse()) : false,
                        maxDate: false,
                        dateFormat: 'dd-mm-yyyy',
                        autoClose: true,
                        onSelect: function(dateText, inst) { 
                            __cs_start_date = $('#cs_start_date').val();
                            __cs_end_date   = $('#cs_end_date').val();
                            __offset = 1;
                            getArchives();
                        }
                    });
});

$(function(){
    var today = new Date();
    //console.log('today', today);
    $("#cs_start_date").datepicker({
        language: 'en',
        minDate: false,
        maxDate: $('#cs_end_date').val() ? new Date($('#cs_end_date').val().split("-").reverse()) : today,
        dateFormat: 'dd-mm-yyyy',
        autoClose: true,
        onSelect: function(dateText, inst) { 
            __cs_start_date = $('#cs_start_date').val();
            __cs_end_date   = $('#cs_end_date').val();
            __offset = 1;
            getArchives();
        }
    });

    $("#cs_end_date").datepicker({
        language: 'en',
        minDate: $('#cs_start_date').val() ? new Date($('#cs_start_date').val().split("-").reverse()) : today,
        maxDate: false,
        dateFormat: 'dd-mm-yyyy',
        autoClose: true,
        onSelect: function(dateText, inst) { 
            __cs_start_date = $('#cs_start_date').val();
            __cs_end_date   = $('#cs_end_date').val();
            __offset = 1;
            getArchives();
        }
    });
});

/** date picker ends, please dont remove the above code */

    function exportArchiveReport()
    {
        var params                  = {};
            params['keyword']       = __keyword;
            params['cs_start_date'] = __cs_start_date;
            params['cs_end_date']   = __cs_end_date;
            params['limit']         = __limit;
            params['offset']        = __offset;
            location.href = webConfigs('admin_url')+'report/export_archive/'+btoa(JSON.stringify(params));
    }
    
</script>
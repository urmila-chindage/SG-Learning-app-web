<?php include_once 'coursebuilder/lecture_header.php'; ?>
<link rel="stylesheet" type="text/css" href="<?php echo assets_url() ?>css/simplebar.css">
<link rel="stylesheet" type="text/css" href="<?php echo assets_url() ?>css/custom.css">
<style type="text/css" media="screen">

.manual-grade{
   position:relative;
   overflow:hidden;
}
.manual-grade:after{
   content: '';
   background: #009200;
   width: 18px;
   height: 18px;
   position: absolute;
   top: -10px;
   left: -9px;
   transform: rotate(-40deg);
}

    .grade-report {
/*        float: left;
        width: 100%;
        top: 100px;
        position: fixed;
        left: 0px;
        height: calc(100% - 60px);*/
        float: left;
        width: 100%;
        top: 100px;
        position: fixed;
        left: 0px;
        height: calc(100% - 100px);
        padding-bottom: 30px;
    }
    .right-top {
        float: left;
        width: calc(100% - 210px);
        height: 150px;
        overflow: hidden;
    }
    .left-bottom {
        float: left;
        width: 200px;
        height: calc(100% - 150px);
        overflow: hidden;
        border-top: solid 1px #ccc;
    }
    .right-bottom {
        float: right;
        width: calc(100% - 200px);
        height: calc(100% - 150px) !important;
        overflow: auto;
        border-top: solid 1px #ccc;
    }
    .grade-report-header{
        display: flex;
        justify-content: space-between;
        padding: 15px;
        border-bottom: 1px solid #e5e5e5;
        min-height: 16.42857143px;
        background: #ffffff;
        color: #444;
    }
    .grade-report-title{
        margin: 0;
        line-height: 1.42857143;
        text-transform: uppercase;
        display: inline-block;
    }
    .grade-report-close{
        font-size: 26px;
        font-weight: 800;
        line-height: 0px;
        float: right;
        padding-top: 12px;
        cursor: pointer;
    }
    .grade-report-filter{width: 100%; z-index:1 !important;}
    .grade-report-search{
        border-left: 1px solid #bcbcbc !important;
        width: 20% !important;
    }
    .alert {
        top: 50px;
        z-index: 1;
    }
    .tooltip{ z-index :999999999 !important; position:absolute;}
    .grade-report-count{    
        margin: 4px;
        padding: 0px 40px 0 0;
        font-size: 16px;
        font-weight: 600;
        color: #33b565;
    }
    /*#container_lecture_status tr{display: flex !important;}*/
</style>
</head>

<body>


<div class="grade-report-header">
    <h4 class="grade-report-title">GRADE REPORT - <span class="text-green"><b><?php echo $selected_course; ?></b></span></h4>
    <div style="display: flex;">
        <h5 class="grade-report-count"><span id="loaded_subscribers"></span>/<span id="total_subscribers"></span></h5>
        <a href="javascript:void(0)" onclick="goBackToGrade()"><span class="grade-report-close">&times;</span></a>
    </div>
</div>
<!-- grade report nav starts -->
<div class="container-fluid grade-report-filter nav-content nav-course-content">
        <div class="row">
            <div class="rTable content-nav-tbl">
                <div class="rTableRow">
                <?php if(!isset($role_manager['institute_id'])): ?> 
                    <?php if (!empty($institutes)): ?>
                    <div class="rTableCell dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" id="filter_institute">All Institutes <span class="caret"></span></a>
                        <ul class="dropdown-menu white inner-scroll">
                            <li><a href="javascript:void(0)" id="filter_institute_all" onclick="filter_institute('all')">All Institutes </a></li>
                            <?php foreach ($institutes as $institute): ?>
                            <li><a href="javascript:void(0)" id="filter_institute_<?php echo $institute['id'] ?>" title="<?php echo $institute['ib_institute_code'] . ' - ' . $institute['ib_name'] ?>" onclick="filter_institute(<?php echo $institute['id'] ?>)"><?php echo $institute['ib_institute_code'] . ' - ' . $institute['ib_name'] ?></a></li>
                            <?php endforeach;?>
                        </ul>
                    </div>
                    <?php endif;?>
                <?php endif;?>

                    <div class="rTableCell dropdown" id="filter_batch_div" <?php echo empty($batches) ? 'style="display:none;"' : '' ?>>
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" id="filter_batch">All Batches <span class="caret"></span></a>
                        <ul class="dropdown-menu white inner-scroll" id="batch_filter_list">
                        <li><a href="javascript:void(0)" id="filter_batch_all" onclick="filter_batch('all')">All Batches </a></li>
                            <?php if (!empty($batches)):
                            $batch_tooltip = '';
                            ?>
                            <?php foreach ($batches as $batch):
                                $batch_tooltip = (strlen($batch['batch_name']) > 15) ? ' title="' . $batch['batch_name'] . '"' : '';
                                ?>
                                <li><a href="javascript:void(0)" id="filter_batch_<?php echo $batch['id'] ?>" <?php echo $batch_tooltip; ?> onclick="filter_batch(<?php echo $batch['id'] ?>)"><?php echo $batch['batch_name'] ?></a></li>
                                <?php endforeach;?>
                            <?php endif;?>
                        </ul>
                    </div>

                    <div class="rTableCell dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" id="filter_report_text">All Students<span class="caret"></span></a>
                        <ul class="dropdown-menu white inner-scroll">
                            <li><a href="javascript:void(0)" onclick="filter_report_by('all')" id="filer_dropdown_list_all"><?php echo 'All Students' ?></a></li>
                            <li><a href="javascript:void(0)" onclick="filter_report_by('completed')" id="filer_dropdown_list_completed"><?php echo 'Completed '?></a></li>
                            <li><a href="javascript:void(0)" onclick="filter_report_by('not_started')" id="filer_dropdown_list_not_started"><?php echo 'Not yet started' ?></a></li>
                            <?php
                            foreach($grades as $grade):
                                ?>
                                <li><a href="javascript:void(0)" onclick="filter_report_by('<?php echo $grade['gr_name'] ?>')" id="filer_dropdown_list_<?php echo $grade['gr_name'] ?>"><?php echo 'Grade '. $grade['gr_name'] ?></a></li>
                                <?php
                            endforeach;
                            ?>
                        </ul>
                    </div>

                    <div class="rTableCell grade-report-search">
                        <div class="input-group">
                            <input type="text" class="form-control srch_txt" id="user_keyword" placeholder="Search">
                            <span id="searchclear" style="display: none;">×</span>
                            <a class="input-group-addon" id="basic-addon2">
                                <i class="icon icon-search"></i>
                            </a>
                        </div>
                    </div>
                    
                    <div class="rTableCell">
                        <div class="save-btn"><button class="pull-right btn btn-green selected" onclick="exportGradeReport();">EXPORT<ripples></ripples></button></div>
                    </div>
                </div>
            </div>
        </div>
</div>
<!-- grade report nav ends here -->

<div class="grade-report" id="course_report_container_wrapper">
    <div class="left-top">
        <div class="load-reports" id="report_load_more" style="display:none;">
            <button class="btn btn-green selected" onclick="getCourseReport(false);">Load More</button>
        </div>
        <div class="z-text completed-percent" title="Assessment and Assignment Grade">Grade</div>
        <div class="z-text completed-grade">% Completed</div>
    </div>
    <div class="right-top" id="right-top">
        <table class="right-top-table" cellpadding="0" cellspacing="0" id="container_lecture_names">
            
        </table>
    </div>
    <div class="left-bottom" id="left-bottom">
        <table class="left-bottom-table" cellpadding="0" cellspacing="0" id="container_student_names">
            
        </table>
    </div>
    <div class="right-bottom" id="right-bottom">
        <table class="right-bottom-table" cellpadding="0" cellspacing="0" id="container_lecture_status">
            
        </table>
    </div>
</div>
<div id="toast"><div id="toastval"></div></div>
<script type="text/javascript" src="<?php echo assets_url() ?>js/simplebar.js"></script>
    <script type="text/javascript">
    var __access_permission    = '<?php echo json_encode($access_permission); ?>';
    var __grades = '';
    var __course_id = '';

    var __subscribers   = '';
    var __lectures      = '';

    var __limit     = '';
    var __offset    = '';
    var __load_more = 'false';
    var __total_subscribers = '';

        var el = new SimpleBar(document.getElementById('right-bottom'));
        el.getScrollElement().addEventListener('scroll', function(){
            document.getElementById('left-bottom').scrollTop=this.scrollTop;
        });
        el.getScrollElement().firstChild.addEventListener('scroll', function(){
        document.getElementById('right-top').scrollLeft=this.scrollLeft;
        });
       

        $(document).ready(function()
        {
            __limit         = '<?php echo $limit; ?>';
            __offset        = 2;
            __load_more     = '<?php echo ($load_more == true)? 'true':'false'; ?>';
            __grades        = '<?php echo json_encode($grades); ?>';
            __course_id     = '<?php echo $course_id; ?>';

            __subscribers   = atob('<?php echo base64_encode(json_encode($subscribers))?>');
            __lectures      = '<?php echo json_encode($lectures) ?>';

            __subscribers   = $.parseJSON(__subscribers);
            __lectures      = $.parseJSON(__lectures);
            __total_subscribers = '<?php echo $total_subscribers; ?>';
            renderGradeReport();
        });

    function goBackToGrade(){
        var link = "<?php echo admin_url() ?>"+"report/course";

        var params = {};
        if (location.search) {
            var parts = location.search.substring(1).split('&');

            for (var i = 0; i < parts.length; i++) {
                var nv = parts[i].split('=');
                if (!nv[0]) continue;
                params[nv[0]] = nv[1] || true;
            }
        }

            if(typeof params['course'] != 'undefined') {
                link += '?course_id=' + params['course'];
                
                if(typeof params['assessment'] != 'undefined') {
                    link += '&quiz_id=' + params['assessment'];
                }
                
            }
            if(typeof params['institute_id'] != 'undefined') {
                link += '&institute_id=' + params['institute_id'];
                
                if(typeof params['batch_id'] != 'undefined') {
                    link += '&batch_id=' + params['batch_id'];
                }
            }
            if(typeof params['filter'] != 'undefined') {
                link += '&filter_by=' + params['filter'];
            }
            location.href= link;
        }

    </script>
    <!-- simple bar plugin ends-->
    <script type="text/javascript" src="<?php echo assets_url() ?>js/grade_report.js"></script>
    
</body>
<?php include_once 'footer.php'; ?>
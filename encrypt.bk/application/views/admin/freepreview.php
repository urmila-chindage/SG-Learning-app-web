<?php include_once 'header.php'; ?>
<?php include_once('report_tab.php') ?>
<link rel="stylesheet" href="<?php echo assets_url() ?>css/redactor/css/redactor.css" />
<link rel="stylesheet" href="<?php echo assets_url() ?>css/redactor/css/alignment.css" />
<link rel="stylesheet" href="<?php echo assets_url() ?>css/datepicker.min.css">
<style>
    #popUpMessagePage {width: 60%;margin: 15px auto;}
    .modal {
    z-index: 1051 !important;
}
</style>
<!-- Free prev report -->
<div class="preview-report-container">
    <div class="free-prev-header">
        <div>
            <h5>Free Preview Report</h5>
        </div>
        <div class="free-prev-action">
            <div class="search-holder">
                <input class="form-control srch_txt" placeholder="Search in course" type="text" id="course_keyword" value="<?=($keyword)?$keyword:''?>">
                <span id="searchclear" style="display: none;height: 35px;right: 40px;">×</span>
                <span class="search-icon">
                <i class="icon icon-search"> </i>
                </span>
            </div>
            <!-- <button class="btn btn-green export-btn" onclick="exportPreview()">Export All</button> -->
        </div>
    </div>
    <div class="col-md-12 pad0 preview-report-content">
        <div class="rTableCell text-center" id ="loading" style="margin-top:15px;" >      
                    Loading.....
        </div>
        <div class="col-md-6 pad0 left-report-container " >
            
            <div id="preview_course_wrapper">.
            
            </div>
            <div class="rTableCell text-center loadmore-block" >      
                <a id="loadmorebutton" style="display:none;"class="btn btn-green selected" onclick="loadMorecourses()">Load More</a>               
            </div>
        </div>
        <div class="col-md-6 pad0 right-report-container" style="display:none;">
            <!--<div class="d-flex justify-between bulk-action-wrapper" id="checkbox-parent" style="display:none;">
                <label >
                    <input class="user-checkbox-parent" type="checkbox">select all<span id="selected_user_count"></span></label>
                    
                </label>
                <div>
                    <div class="btn-group lecture-control btn-right-align" style="margin: 0px; display: none;" id="user_bulk">
                        <span class="dropdown-tigger" data-toggle="dropdown" aria-expanded="false">  
                            <span class="label-text">  Bulk Action</span>  
                            <span class="tilder"></span>
                        </span>
                        <ul class="dropdown-menu pull-right" role="menu"> 
                            <li> <a href="javascript:void(0);" onclick="sendMessageToUser()">Send Message</a> </li>    
                        </ul>
                    </div>
                </div>
            </div> -->  
            <div id="preview_course_users" class="previewed-user-list">
                loading...
            </div>     
        </div>
    </div>
</div>
<!-- Free prev report ends -->



<script type="text/javascript" src="<?php echo assets_url() ?>js/jquery.slimscroll.min.js"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/free_preview.js?v=0.1"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/datepicker.js"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/datepicker.en.js"></script>
<?php
    $remaining_courses = $total_courses - sizeof($previews);
    $remaining_courses = ($remaining_courses>0)?'('.$remaining_courses.')':'';
?>
<script>
    var __userSelected          = new Array();
    var __disabled              = 'disabled="disabled"';
    var __checked               = 'checked="checked"';
    var __PreviewCourseObject   = new Array;
        __PreviewCourseObject   = atob('<?php echo base64_encode(json_encode($previews)) ?>');
    var __keyword               = '';
    var __count                 = 0;
    var __courseKeyword         = '<?php echo (($keyword)?$keyword:'') ?>';
    var __limit                 = '<?php echo $limit; ?>';
    var __offset                = 2;
    var __remainingCourses      = '<?php echo $remaining_courses?>';
    var __showLoadButton        = '<?php echo $show_load_button?>';
</script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/multi-select/jquery.tokenize.js"></script>
<script src="<?php echo assets_url() ?>js/redactor/js/redactor.js"></script>
<script src="<?php echo assets_url() ?>js/redactor/js/table.js"></script>
<script src="<?php echo assets_url() ?>js/redactor/js/alignment.js"></script>
<script src="<?php echo assets_url() ?>js/jquery.slimscroll.min.js"></script>
<?php // include_once('preview_modals.php') ?>
<?php include_once 'footer.php'; ?>
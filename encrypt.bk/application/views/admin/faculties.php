<?php //echo '<pre>'; print_r($faculties);die; ?>
<?php include_once 'header.php'; ?>
<link rel="stylesheet" type="text/css" href="<?php echo assets_url() ?>css/jquery.rateyo.min.css">
<style type="text/css">
.wrap { 
   word-wrap: break-word; 
   white-space: pre-wrap;       
   white-space: -moz-pre-wrap;
   white-space: -pre-wrap;     
   white-space: -o-pre-wrap;        
}
.trim-text{
    text-overflow: ellipsis;
    overflow: hidden;
}
.tooltip-inner {
    max-width: 300px;
    min-width: max-content;
}
</style>
<section class="content-wrap create-group-wrap settings-top">
    <!-- LEFT CONTENT --> <!-- STARTS -->
    <!-- ===========================  -->
    <div class="col-sm-12 pad0 settings-left-wrap">
        <!-- Group content section  -->
        <!-- ====================== -->
        <div class="col-sm-12 nav-content faculty-nav-content">
            <div class="row">
                <div class="rTable content-nav-tbl" style="">
                    <div class="rTableRow d-flex align-center justify-between">

                        <div class="rTableCell" style="width:15%;">
                            <a href="#!." class="select-all-style" id="select_all"><label> <input class="faculty-checkbox-parent" type="checkbox">  Select All</label><span id="selected_faculties_count"></span></a>
                        </div>
                        <div class="rTableCell dropdown" style="width:15%;">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" id="filter_dropdown_text"> <?php echo lang('active_faculties') ?> <span class="caret"></span></a>
                            <ul class="dropdown-menu white">
                                <li><a href="javascript:void(0)" id="filer_dropdown_list_all" onclick="filter_faculties_by('all')"><?php echo lang('all_faculties') ?></a></li>
                                <li><a href="javascript:void(0)" id="filer_dropdown_list_inactive" onclick="filter_faculties_by('inactive')"><?php echo lang('inactive_faculties') ?></a></li>
                                <li><a href="javascript:void(0)" id="filer_dropdown_list_active" onclick="filter_faculties_by('active')"><?php echo lang('active_faculties') ?></a></li>
                                <li role="separator" class="divider"></li>
                                <li><a href="javascript:void(0)" id="filer_dropdown_list_deleted" onclick="filter_faculties_by('deleted')"><?php echo lang('deleted_faculties') ?></a></li>
                            </ul>
                        </div>
                        <div class="rTableCell dropdown" style="width:10%;">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" id="dropdown_role_text" data-role-id="0">All Roles<span class="caret"></span></a>
                            <ul class="dropdown-menu white">
                                <li><a class="trim-text"  data-placement="right" href="javascript:void(0)" id="dropdown_list_0" onclick="filter_faculty(0)"><?php echo 'All Roles' ?></a></li>
                                <?php if(!empty($roles)): ?>
                                <?php foreach($roles as $role): ?>
                                <li><a class="trim-text" data-toggle="tooltip" data-placement="right" title="<?php echo $role['rl_name']?>" href="javascript:void(0)" id="dropdown_list_<?php echo $role['id'] ?>" onclick="filter_faculty(<?php echo $role['id'] ?>)"><?php echo (strlen($role['rl_name']) > 20)? substr($role['rl_name'],0,17).'...' : $role['rl_name']; ?></a></li>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </ul>
                        </div>
                        <div class="rTableCell" style="width:25%;">
                            <div class="input-group">
                                <input class="form-control srch_txt" id="faculty_keyword" placeholder="Search by Name" type="text">
                                <span id="searchclear">×</span>
                                <a class="input-group-addon" id="faculty_search">
                                    <i class="icon icon-search"> </i>
                                </a>
                            </div> 
                        </div>
                        <div class="rTableCell" style="border:0px;width:10%;">
                            <!-- lecture-control start -->
                            <div class="btn-group lecture-control btn-right-align" id="bulk_action_wrapper" style="margin-top: 0px; display: none;">
                                <span class="dropdown-tigger" data-toggle="dropdown" aria-expanded="false">
                                    <span class="label-text">
                                       Bulk Action  <!-- <span class="icon icon-down-arrow"></span> -->
                                    </span>
                                    <span class="tilder"></span>
                                </span>
                                <ul class="dropdown-menu pull-right" role="menu">
                                <?php
                                    if(in_array('1', $permissions)):
                                        ?>
                                    <li><a href="javascript:void(0);" onclick="sendMessageToFaculty(0)"><?php echo lang('send_message') ?></a></li>
                                    <?php
                                    endif;
                                    if(in_array('4', $permissions)):
                                        ?>
                                    <li><a href="javascript:void(0);" onclick="deleteFacultyBulk()"><?php echo lang('delete_account') ?></a></li>
                                    <?php
                                    endif;
                                    if(in_array('3', $permissions)):
                                        ?>
                                    <li><a href="javascript:void(0);" onclick="changeStatusBulk(1)"><?php echo lang('account').' '.lang('activate') ?></a></li>
                                    <li><a href="javascript:void(0);" onclick="changeStatusBulk(0)"><?php echo lang('account').' '.lang('deactivate') ?></a></li>
                                    <?php
                                    endif;
                                ?>   
                                </ul>
                            </div>
                            <!-- lecture-control end -->
                        </div>
                        <?php
                        if(in_array('2', $permissions)):
                            ?>
                            <div class="rTableCell d-flex align-center" style="border:0px;width:15%;justify-content: flex-end;">
                                <a href="javascript:void(0);" data-toggle="modal" data-target="#import-faculties" id="bulk_import_faculties" class="btn btn-blue txt-left">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="#fff" height="24" viewBox="0 0 24 24" width="24" style=" vertical-align: middle; margin-right: 9px;"><path class="heroicon-ui" d="M11 14.59V3a1 1 0 0 1 2 0v11.59l3.3-3.3a1 1 0 0 1 1.4 1.42l-5 5a1 1 0 0 1-1.4 0l-5-5a1 1 0 0 1 1.4-1.42l3.3 3.3zM3 17a1 1 0 0 1 2 0v3h14v-3a1 1 0 0 1 2 0v3a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-3z"></path></svg>
                                    <?php echo 'IMPORT' ?>
                                </a>
                            </div>
                            <div class="rTableCell d-flex align-center" style="width:15%;justify-content: flex-end;">
                                <button class="pull-right btn btn-green" onclick="addFacultyForm();"><i class="bigicon icon icon-user"></i>ADD FACULTY</button>
                            </div>
                            <?php
                        endif;
                        ?>                        
                    </div>
                </div>
            </div>
        </div>                

        <div class="col-sm-12 group-content course-cont-wrap list-faculty-wrap"> 
            <div class="table course-cont rTable" style="" id="faculty_wrapper">
            </div>   
            <?php
            $remaining_faculties = $total_faculties - sizeof($faculties);
            $remaining_faculties = ($remaining_faculties>0)?'('.$remaining_faculties.')':'';
            ?>
            <div class="rTableCell text-center" >      
                <a id="loadmorebutton" <?php echo ((!$show_load_button)?'style="display:none;"':'') ?>  class="btn btn-green selected " onclick="loadMoreFaculties()">Load More <?php echo $remaining_faculties ?><ripples></ripples></a>               
            </div>        
        </div>
        <!-- ====================== -->
        <!-- Group content section  -->
    </div>
    <!-- ==========================  -->
    <!--  LEFT CONTENT--> <!-- ENDS -->
</section>

<div class="col-sm-6 pad0 right-content faculty-right-content">
    <div class="container-fluid right-box">
        <div class="row overflow100">
            <div class="col-sm-12 course-cont-wrap image-uploader faculty innercontent" id="faculty_detail_wrapper">
            </div>
        </div>
    </div>
</div>
<?php include_once 'footer.php'; ?>
<script>
    var __site_url          = '<?php echo site_url() ?>';
    var __facultyObject     = new Array;
    var __facultylanguages  = new Array;
        __facultyObject     = atob('<?php echo base64_encode(json_encode($faculties)) ?>'); 
        __facultylanguages  = atob('<?php echo base64_encode(json_encode($languages)) ?>');         
        if(typeof __facultylanguages != 'object')
        {
            __facultylanguages  = $.parseJSON(__facultylanguages);         
        }
    var __limit         = '<?php echo $limit; ?>';
    var __offset        = 2;

    var __permissions    = '<?php echo json_encode($permissions); ?>';
        __permissions    = $.parseJSON(__permissions);
    var course_faculty_permissions = '<?php echo json_encode($course_privilege); ?>';
    let __assign_faculty = new User(course_faculty_permissions);
    var full_course_access ='<?php echo $full_course_access; ?>';
</script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/faculty.js"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/jquery.slimscroll.min.js"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/multi-select/jquery.tokenize.js"></script>
<script src="<?php echo assets_url() ?>js/redactor/js/redactor.js"></script>
<script src="<?php echo assets_url() ?>js/redactor/js/table.js"></script>
<script src="<?php echo assets_url() ?>js/redactor/js/alignment.js"></script>
<script src="<?php echo assets_url() ?>js/jquery.rateyo.min.js"></script>

<script>
$(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip({trigger: "hover"}); 
    
});
</script>

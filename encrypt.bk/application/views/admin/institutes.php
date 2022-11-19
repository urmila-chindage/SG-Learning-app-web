<?php //echo '<pre>'; print_r($institutes);die; ?>
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

.faculty-right-content::-webkit-scrollbar {width: 10px;}
.faculty-right-content::-webkit-scrollbar-track {background: #f1f1f1;}
.faculty-right-content::-webkit-scrollbar-thumb {background: #a1a1a159; }
.faculty-right-content::-webkit-scrollbar-thumb:hover {background: #555; }

.faculty-right-content{overflow: auto;}
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
                    <div class="rTableRow">

                        <div class="rTableCell">
                            <a href="#!." class="select-all-style" id="select_all"><label> <input class="institute-checkbox-parent" type="checkbox">  Select All</label><span id="selected_institutes_count"></span></a>
                        </div>
                        <div class="rTableCell dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" id="filter_dropdown_text"> <?php echo lang('active_institutes') ?> <span class="caret"></span></a>
                            <ul class="dropdown-menu white">
                                <li><a href="javascript:void(0)" id="filer_dropdown_list_all" onclick="filter_institutes_by('all')"><?php echo lang('all_institutes') ?></a></li>
                                <li><a href="javascript:void(0)" id="filer_dropdown_list_inactive" onclick="filter_institutes_by('inactive')"><?php echo lang('inactive_institutes') ?></a></li>
                                <li><a href="javascript:void(0)" id="filer_dropdown_list_active" onclick="filter_institutes_by('active')"><?php echo lang('active_institutes') ?></a></li>
                                <li role="separator" class="divider"></li>
                                <li><a href="javascript:void(0)" id="filer_dropdown_list_deleted" onclick="filter_institutes_by('deleted')"><?php echo lang('deleted_institutes') ?></a></li>
                            </ul>
                        </div>
                        <div class="rTableCell">
                            <div class="input-group">
                                    <input class="form-control srch_txt" id="institute_keyword" placeholder="Search by name or college code" type="text">
                                    <span id="searchclear">&times;</span>
                                    <a class="input-group-addon" id="institute_search">
                                        <i class="icon icon-search"> </i>
                                    </a>
                            </div> 
                        </div>
                        <div class="rTableCell">
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
                                        <li><a href="javascript:void(0);" onclick="sendMessageToInstitute(0)"><?php echo lang('send_message') ?></a></li>
                                        <?php
                                    endif;
                                    if(in_array('4', $permissions)):
                                        ?>
                                        <li><a href="javascript:void(0);" onclick="deleteInstituteBulk()"><?php echo lang('delete').' Institutes' ?></a></li>
                                        <?php
                                    endif;
                                    if(in_array('3', $permissions)):
                                        ?>
                                        <li><a href="javascript:void(0);" onclick="changeStatusBulk(1)"><?php echo lang('activate').' Institutes' ?></a></li>
                                        <li><a href="javascript:void(0);" onclick="changeStatusBulk(0)"><?php echo lang('deactivate').' Institutes' ?></a></li>
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
                            <div class="rTableCell text-right">
                                <a href="javascript:void(0);" data-toggle="modal" data-target="#import-institutes"  class="btn btn-blue txt-left">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="#fff" height="24" viewBox="0 0 24 24" width="24" style=" vertical-align: middle; margin-right: 9px;"><path class="heroicon-ui" d="M11 14.59V3a1 1 0 0 1 2 0v11.59l3.3-3.3a1 1 0 0 1 1.4 1.42l-5 5a1 1 0 0 1-1.4 0l-5-5a1 1 0 0 1 1.4-1.42l3.3 3.3zM3 17a1 1 0 0 1 2 0v3h14v-3a1 1 0 0 1 2 0v3a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-3z"></path></svg>
                                    <?php echo 'IMPORT' ?>
                                </a>
                                <a href="javascript:void(0);" class="btn btn-blue txt-left" onclick="exportInstitutes()">
                                <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                                width="24px" height="24px" style="vertical-align: middle;margin-right: 9px;" viewBox="0 0 24 24" enable-background="new 0 0 24 24" xml:space="preserve">
                                <path fill="#FFFFFF" d="M13,5.406v11.59c0,0.553-0.447,1-1,1s-1-0.447-1-1V5.406l-3.3,3.3C7.278,9.063,6.646,9.01,6.291,8.588
                                C5.973,8.211,5.977,7.658,6.3,7.286l5-5c0.389-0.382,1.011-0.382,1.4,0l5,5c0.362,0.417,0.318,1.049-0.099,1.411
                                c-0.372,0.323-0.925,0.327-1.302,0.009L13,5.406z M3,17c0-0.553,0.448-1,1-1s1,0.447,1,1v3h14v-3c0-0.553,0.447-1,1-1s1,0.447,1,1v3
                                c0,1.104-0.896,2-2,2H5c-1.104,0-2-0.896-2-2V17z"/></svg>
                                    EXPORT
                                </a>
                            </div>
                            <div class="rTableCell">
                                <div class="save-btn"><button class="pull-right btn btn-green" onclick="addInstituteForm();">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="#fff" height="24" stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" width="24" style="vertical-align: middle;margin-right: 5px;"><line x1="12" x2="12" y1="5" y2="19"></line><line x1="5" x2="19" y1="12" y2="12"></line></svg>
                                ADD INSTITUTE</button></div>
                            </div>
                            <?php
                        endif;
                        ?>
                    </div>
                </div>
            </div>
        </div>                

        <div class="col-sm-12 group-content course-cont-wrap list-faculty-wrap"> 
            <div class="table course-cont rTable" style="" id="institute_wrapper">
            </div> 
            <?php
            $remaining_institutes = $total_institutes - sizeof($institutes);
            $remaining_institutes = ($remaining_institutes>0)?'('.$remaining_institutes.')':'';
            ?>
            <div class="rTableCell text-center" >      
                <a id="loadmorebutton" <?php echo ((!$show_load_button)?'style="display:none;"':'') ?>  class="btn btn-green selected " onclick="loadMoreInstitutes()">Load More <?php echo $remaining_institutes ?><ripples></ripples></a>               
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
            <div class="col-sm-12 course-cont-wrap image-uploader faculty innercontent" id="institute_detail_wrapper">
            </div>
        </div>
    </div>
</div>
<script>
    var __site_url          = '<?php echo site_url() ?>';
    var __instituteObject     = new Array;
    var __institutelanguages  = new Array;
        __instituteObject     = atob('<?php echo base64_encode(json_encode($institutes)) ?>'); 
        __institutelanguages  = atob('<?php echo base64_encode(json_encode($languages)) ?>');         
        if(typeof __institutelanguages != 'object')
        {
            __institutelanguages  = $.parseJSON(__institutelanguages);         
        }
    var __limit         = '<?php echo $limit; ?>';
    var __offset        = 2;

    var __permissions   = '<?php echo json_encode($permissions); ?>';
        __permissions   = $.parseJSON(__permissions);
</script>
<?php include_once 'footer.php'; ?>
<script type="text/javascript" src="<?php echo assets_url() ?>js/institute.js"></script>
<!-- <script type="text/javascript" src="<?php echo assets_url() ?>js/jquery.slimscroll.min.js"></script> -->
<script type="text/javascript" src="<?php echo assets_url() ?>js/multi-select/jquery.tokenize.js"></script>
<script src="<?php echo assets_url() ?>js/redactor/js/redactor.js"></script>
<script src="<?php echo assets_url() ?>js/redactor/js/table.js"></script>
<script src="<?php echo assets_url() ?>js/redactor/js/alignment.js"></script>
<script src="<?php echo assets_url() ?>js/jquery.rateyo.min.js"></script>

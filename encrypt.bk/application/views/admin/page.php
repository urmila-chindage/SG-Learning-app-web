<?php include_once "header.php"; ?> 
<style>
.btn-group.lecture-control { margin: 0px 8px 0px 0px; }
</style>
<?php
$fullwidth_class = 'nopad';
if(in_array($this->__access['add'], $this->__page_privilege)):
$fullwidth_class = ''; 
?>
<div class="right-wrap base-cont-top container-fluid pull-right">
    <div class="row">
        <div class="col-sm-12">
            <a href="javascript:void(0)" class="btn btn-green btn-big full-width-btn" data-toggle="modal" data-target="#create_page" onclick="createPage('<?php echo lang('create_new_page') ?>', '<?php echo lang('page_title') ?>*:');">
                <?php echo lang('create_new_page') ?>
            </a>
        </div>
    </div>
</div>
<?php endif; ?>
<?php /*
<script type="text/javascript" src="<?php echo assets_url()?>/js/jquery-ui.min.js"></script>
<script>
    var __admin_url           = '<?php echo admin_url() ?>';
    $(document).ready(function() {
        $("#page_row_wrapper").sortable({
            connectWith: ["#page_row_wrapper"],
            stop: function() {
                var pages = []
                    $('.dragging').each(function(){
                        pages.push($(this).attr('id').replace(/page_row_/g, ''))
                    });
                $.ajax({
                    url: __admin_url+'page/update_page_position',
                    type: "POST",
                    async:true,
                    data:{ "is_ajax":true, "page_id":pages},
                    success: function(response) {
                        //console.log(response);
                    }
                });
                 //console.log(pages);
            }
        });
    });
</script>
*/ ?>
<section class="content-wrap base-cont-top <?php echo $fullwidth_class; ?>">
    <div class="container-fluid nav-content nav-course-content">
        <div class="row">
            <div class="rTable content-nav-tbl" style="">
                <div class="rTableRow d-flex justify-between">
                    <div class="rTableCell">
                        <a href="javascript:void(0)" class="select-all-style">
                            <label> 
                                <input class="page-checkbox-parent" type="checkbox"><?php echo lang('select_all') ?>
                            </label>
                            <span id="selected_page_count"></span>
                        </a>
                    </div>
                    <div class="rTableCell dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" id="filter_dropdown_text"> <?php echo lang('all_pages') ?> <span class="caret"></span></a>
                        <ul class="dropdown-menu white">
                            <li><a href="javascript:void(0)" id="filer_dropdown_list_all" onclick="filter_page_by('all')"><?php echo lang('all_pages') ?></a></li>
                            <li><a href="javascript:void(0)" id="filer_dropdown_list_inactive" onclick="filter_page_by('inactive')"><?php echo lang('inactive_pages') ?></a></li>
                            <li><a href="javascript:void(0)" id="filer_dropdown_list_active" onclick="filter_page_by('active')"><?php echo lang('active_pages') ?></a></li>
                            <li role="separator" class="divider"></li>
                            <li><a href="javascript:void(0)" id="filer_dropdown_list_deleted" onclick="filter_page_by('deleted')"><?php echo lang('deleted_pages') ?></a></li>
                        </ul>
                    </div>
                    <!--<div class="rTableCell dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" id="show_page_in_dropdown_text"> <?php echo lang('show_page_in') ?> <span class="caret"></span></a>
                        <ul class="dropdown-menu white">
                            <li><a href="javascript:void(0)" id="filer_dropdown_position_anywhere" onclick="filter_page_position_by('anywhere')"><?php echo lang('anywhere') ?></a></li>
                            <li><a href="javascript:void(0)" id="filer_dropdown_position_header" onclick="filter_page_position_by('header')"><?php echo lang('header') ?></a></li>
                            <li><a href="javascript:void(0)" id="filer_dropdown_position_footer" onclick="filter_page_position_by('footer')"><?php echo lang('footer') ?></a></li>
                            <li><a href="javascript:void(0)" id="filer_dropdown_position_headerfooter" onclick="filter_page_position_by('headerfooter')"><?php echo lang('headerfooter') ?></a></li>
                            <li><a href="javascript:void(0)" id="filer_dropdown_position_nowhere" onclick="filter_page_position_by('nowhere')"><?php echo lang('nowhere') ?></a></li>
                        </ul>
                    </div>-->
                    <div class="rTableCell">
                        <div class="input-group">
                            <input type="text" class="form-control srch_txt" id="page_keyword" placeholder="<?php echo lang('search_by_name') ?>" />
                            <span id="searchclear" style="">×</span>
                            <a class="input-group-addon" id="basic-addon2">
                                <i class="icon icon-search"> </i>
                            </a>
                        </div> 
                    </div>
                    <div class="rTableCell" >
                        <div class="btn-group lecture-control btn-right-align" style="margin-top: 0px; display:none;" id="page_bulk">
                            <span class="dropdown-tigger" data-toggle="dropdown" style="padding: 2px 10px;">
                                <span class='label-text'>
                                    <?php echo lang('bulk_action') ?>  <!-- <span class="icon icon-down-arrow"></span> -->
                                </span>
                                <span class="tilder"></span>
                            </span>
                            <ul class="dropdown-menu pull-right" role="menu">
                                <li>
                                    <a href="javascript:void(0)" onclick="deletePageBulk('<?php echo lang('delete_selected_pages') ?>', '1')"><?php echo lang('delete_pages') ?></a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)" onclick="changePageStatusBulk('<?php echo lang('activate_selected_pages') ?>', '1')"><?php echo lang('page_activate') ?></a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)" onclick="changePageStatusBulk('<?php echo lang('deactivate_selected_pages') ?>', '0')"><?php echo lang('page_deactivate') ?></a>
                                </li>
                                <li id="bulkRestorePage">
                                    <a href="javascript:void(0)" onclick="restorePageBulk('<?php echo lang('restore_selected_pages') ?>', '0')"><?php echo lang('restore_pages') ?></a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="left-wrap pad0">
        <div class="container-fluid">
            <div class="row">   
                <div class="col-sm-12 course-cont-wrap " id="show_message_div" style="margin-bottom:60px;">
                    <div class="pull-right">
                       <h4 class="right-top-headedfgr course-count"><span id="total-pages"></span></h4>
                    </div> 
                    <div class="table course-cont only-course rTable ui-sortable" style="" id="page_row_wrapper" >
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div id="pagination_wrapper">
        </div>
    </div>
</section>
<?php //echo '<pre>'; print_r($total_pages); ?>
<script type="text/javascript">

//alert(lang('active'));
var __pages                 = atob('<?php echo base64_encode(json_encode($pages)) ?>');
var __limit                 = '<?php echo $limit ?>';
var __totalPages            = '<?php echo $total_pages; ?>';
//console.log(__totalPages, 'pages');
var __offset                = <?php echo isset($offset)?$offset:'1'; ?>;
const __previlages__        = atob('<?php echo base64_encode(json_encode($this->__page_privilege)) ?>');
var __site_url              = '<?php echo site_url();?>';
</script>
<script src="<?php echo assets_url() ?>js/page.js"></script>
<?php include_once 'footer.php'; ?>


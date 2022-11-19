<?php include_once 'header.php';?>
<style>

    .content-wrap1 {
        padding-right: 0px;
    }
    .cours-fix{ padding: 0px 0px 0px 15px !important;}
    .cours-fix a {display: inline-block; width: 100%; padding: 5px 0;}
    .display-initial{display: initial !important;}
</style>
<?php $actions = config_item('actions'); ?>
    <?php
    $fullwidth_class = 'nopad';
    if(in_array($this->__access['add'], $this->__bundle_privilege)):
    $fullwidth_class = '';  
    ?>
    <div class="right-wrap base-cont-top container-fluid pull-right">
        <a href="javascript:void(0)" class="btn btn-green btn-big full-width-btn" onclick="createBundle();">
            <?php echo lang('create_new_bundle') ?>
        </a>
    </div>
    <?php endif; ?>
    
    <!-- SIDEBAR RIGHT SIDE FIXED --> <!-- END -->

    <section class="content-wrap base-cont-top <?php echo $fullwidth_class; ?>">

        <div class="container-fluid nav-content nav-course-content">

            <div class="row">
                <div class="rTable content-nav-tbl" style="">
                    <div class="rTableRow d-flex">
                        <div class="rTableCell" style="width: 170px" >
                            <a href="javascript:void(0)" class="select-all-style"><label> 
                            <input class="course-checkbox-parent" type="checkbox"><?php  echo lang('select_all') ?><span id="selected_course_count"></span></label></a>

                        </div>
                     
                        <div class="rTableCell dropdown" style="width: 250px">

                        <a href="#" class="dropdown-toggle"  data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" id="filter_dropdown_text"><?php echo lang('active_bundles') ?><span class="caret"></span></a>
                            
                            <ul class="dropdown-menu white">
                                    <li><a href="javascript:void(0)" id="filer_dropdown_list_all" onclick="filter_course_by('all')"><?php echo lang('all_bundles') ?></a></li>
                                    <li><a href="javascript:void(0)" id="filer_dropdown_list_inactive" onclick="filter_course_by('inactive')"><?php echo lang('inactive_bundles') ?></a></li>
                                    <li><a href="javascript:void(0)" id="filer_dropdown_list_active" onclick="filter_course_by('active')"><?php echo lang('active_bundles') ?></a></li>
                                    <li><a href="javascript:void(0)" id="filer_dropdown_list_deleted" onclick="filter_course_by('deleted')"><?php echo lang('deleted_bundles') ?></a></li>
                                </ul>
                        </div>
                        
                        <div class="rTableCell dropdown challenge-zone-drop" style="width: 250px">

                                <a href="#" class="dropdown-toggle"   data-toggle="dropdown" role="button" title="" aria-haspopup="true" aria-expanded="false" id="dropdown_text"><span title="All Category" class="category-text-ellipsis" > All <?php echo lang('category') ?></span>
                                    <span class="caret" style="margin-top: -8px;"></span>
                                </a>
                                    <ul class="dropdown-menu white" style="min-width: 225px !important; width:auto;">
                                        <li>
                                            <a href="javascript:void(0)" id="dropdown_list_all" onclick="filter_category('all')">All <?php echo lang('category') ?></a></li>
                                        <li><a href="javascript:void(0)" id="dropdown_list_uncategorised" onclick="filter_category('uncategorised')"><?php echo lang('uncategorized') ?> </a></li>
                                        <?php if(!empty($categories)): ?>
                                        <?php foreach($categories as $category): ?>
                                            <?php if(strip_tags($category['ct_name'])): ?>
                                                <li><a href="javascript:void(0)" id="dropdown_list_<?php echo $category['id'] ?>" onclick="filter_category(<?php echo $category['id'] ?>)"><?php echo $category['ct_name'] ?></a></li>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                        <?php endif; ?>
                                    </ul>
                            </div>
                            <div class="rTableCell">
                                <div class="input-group">
                                    <input type="text" class="form-control srch_txt" id="course_keyword" placeholder="<?php echo lang('search_by_name') ?>" />
                                    <span id="searchclear">&times;</span>
                                    <a class="input-group-addon" id="basic-addon2">
                                        <i class="icon icon-search"> </i>
                                    </a>
                                </div> 
                            </div>
                            
                            <div class="rTableCell" >
                                <!-- lecture-control start -->
                                <?php if(in_array($this->__access['edit'], $this->__bundle_privilege)): ?>
                                <div class="btn-group lecture-control btn-right-align" style="margin-top: 0px; display:none;" id="course_bulk">
                                    <span class="dropdown-tigger" data-toggle="dropdown" style="padding: 2px 10px;">
                                        <span class='label-text'>
                                           Bulk Action  <!-- <span class="icon icon-down-arrow"></span> -->
                                        </span>
                                        <span class="tilder"></span>
                                    </span>
                                    <ul class="dropdown-menu pull-right" role="menu">
                                        <li><a href="javascript:void(0)" class="list-disabled list-button" onclick="changeStatusBulk('1')"><?php echo lang('activate') ?> </a></li>
                                        <li><a href="javascript:void(0)" class="list-disabled list-button" onclick="changeStatusBulk('0')"><?php echo lang('deactivate') ?> </a></li>
                                    </ul>
                                </div>
                                <?php endif; ?>
                                <!-- lecture-control end -->
                            </div>
                    </div>
                </div>

            </div>
        </div>
        
        <div class="left-wrap col-sm-12">
            <div class="row">   
                <div class="col-sm-12 course-cont-wrap wrap-fix-course" id="show_message_div"> 
                    <div >
                        <div class="pull-right">
                            <!-- Header left items -->
                            <h4 class="right-top-header course-count">
                                <?php 
                                $bundle_html  = '';
                                if($total_bundles < 1) {
                                    $bundle_html = 'No Bundles';
                                } else {
                                    $bundle_html .= sizeof($bundles).' / '.$total_bundles;
                                    $bundle_html .= ($total_bundles>1)?' Bundles':' Bundle';    
                                }
                                echo $bundle_html;
                                $remaining_bundle = $total_bundles - sizeof($bundles);
                                $remaining_bundle = ($remaining_bundle>0)?'('.$remaining_bundle.')':'';
                                ?>
                            </h4>
                        </div>
                    </div>
                    <div class="table course-cont only-course rTable" id="course_row_wrapper">
                        <?php 
                        if(!empty($bundles)){ 
                        ?>
                        <?php 
                            foreach ($bundles as $bundle){ 
                        ?>
                                <div class="rTableRow bundle-list" id="course_row_<?php echo $bundle['id'] ?>" data-title="<?php echo $bundle['c_title'] ?>" >
                            
                                    <?php 
                                        // Get bundle items count
                                        $bundle_items  = json_decode($bundle['c_courses'],true);
                                        $items_size    = (sizeof($bundle_items)>0)?count($bundle_items):0;
                                        $items_counts  = (sizeof($bundle_items)>1)?' Items':' Item'; 
                                        $bundle_items_count = $items_size.$items_counts;
                                        //consider the record is deleted and set the value if record deleted
                                        $label_class    = 'spn-delete';
                                        $action_class   = 'label-danger';
                                        //case if record is not deleted
                                        $item_deleted   = 'item-deleted';
                                        $action         = lang('deleted');
                                        $item_inactive  = '' ;
                                        if($bundle['c_deleted'] == 0)
                                        {
                                            $item_deleted = '';
                                
                                            switch($bundle['c_status'])
                                            {
                                                case 1:
                                                    $action_class   = 'label-success';
                                                    $label_class    = 'spn-active';                                        
                                                    $action         = lang('bundle_active');
                                                    $change_status  = 'deactivate';
                                                break;
                                                case 2:
                                                    $action_class   = 'label-warning';
                                                    $item_inactive  = 'item_inactive';                               
                                                    $label_class    = 'spn-inactive';                                        
                                                    $action   = lang('pending_approval');
                                                    $change_status  = 'activate';
                                                break;
                                                default :
                                                    $action_class   = 'label-warning';
                                                    $item_inactive  = 'item_inactive';                                 
                                                    $label_class    = 'spn-inactive';                                        
                                                    $action         = lang('bundle_inactive');
                                                    $change_status  = '';
                                                break;
                                            }
                                        }
                                    ?>
                                    <div class="rTableCell cours-fix ellipsis-hidden"> 
                                        <div class="ellipsis-style display-initial">  
                                        <?php 
                                            $is_disabled = ($bundle['c_deleted'] == 1)? 'disabled="disabled"' : '';
                                        ?>
                                            <input type="checkbox" <?php echo $is_disabled; ?> class="course-checkbox <?php echo $item_deleted.' '.$item_inactive ?>" value="<?php echo $bundle['id'] ?>" id="course_details_<?php echo $bundle['id'] ?>"> 
                                            
                                            <?php if($bundle['c_deleted'] != 1): ?>
                                                <a href="<?php echo admin_url('bundle/overview/'.$bundle['id']) ?>" class="cust-sm-6 padd0">
                                            <?php endif; ?>
                                            <span class="icon-wrap-round color-box bundle-icon" data-name="<?php echo $bundle['c_title'] ?>">
                                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Layer_1" x="0px" y="0px" viewBox="0 0 510.4 334" style="enable-background:new 0 0 510.4 334;" xml:space="preserve">
                                                    <style type="text/css">
                                                        .st0{ fill:#fdeeee; }
                                                    </style>
                                                    <g>
                                                        <path class="st0" d="M218,236.5L87.9,193.3v42.5v29.7c0,33.4,58.3,60.4,130.1,60.4c71.8,0,130.1-27,130.1-60.4   c0-0.3-0.1-0.5-0.1-0.8v-71.4L218,236.5z"/>
                                                        <path class="st0" d="M0,143.6l46.5,17.9l4-9.2l17.1-1.6l2.4,2.7l-14.6,3.7l-2.1,6.8c0,0-33.1,74.6-28.2,111.2c0,0,20.7,13.3,41.3,0   l5.5-99.8v-8.3l30.7-7.5l-2.2,5.8l-22.9,8l10.6,4.1L218,220.8l130.1-43.2l87.9-33.9L218,53.1L0,143.6z"/>
                                                    </g>
                                                    <path class="st0" d="M371.1,267.5c0,0-1.3,15.3-8.2,28.2c38.1-10.5,63.6-29.8,63.6-51.9c0-0.3-0.1-0.5-0.1-0.8v-71.4l-55.6,18.5l0,0  L371.1,267.5z"/>
                                                    <polygon  class="st0" points="514.4,122 296.4,31.4 254,49 468.1,139.8 "/>
                                                </svg>
                                            </span>
                                            <span class="institution-code"><?php echo $bundle['c_code'].'</span> - ' ?>
                                                <?php echo $bundle['c_title'] ?> <span> (<?php echo $bundle_items_count; ?>)</span>
                                            <?php if($bundle['c_deleted'] != 1): ?>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                            
                                    <?php
                                        $c_date='';
                                        if($bundle['c_access_validity'] == 0){
                                            $c_date = '<span class="text-green">Unlimited</span>';
                                        }else if($bundle['c_access_validity'] == 1){
                                            $c_date = '<span class="text-green">'.$bundle['c_validity']." days</span>";
                                        }else if($bundle['c_access_validity'] == 2){

                                            $current_date   = strtotime(date("d-m-Y",time())); 
                                            $db_date        = strtotime($bundle['c_validity_date']);
                                            if($current_date > $db_date){
                                                $c_date = '<span class="text-red">Expired</span>';
                                            }else if($current_date==$db_date){
                                                $c_date = '<span class="text-orange">Today</span>';
                                            }else{
                                                $c_date ='<span class="text-green">'.date("d-m-Y",$db_date).'</span>';
                                            }
                                        }
                                    ?>
                                    <div class="rTableCell text-center ">
                                        <?php echo $c_date; ?>
                                    </div> 
                                    <div class="rTableCell pad0 cours-fix width70"> 
                                        <div class="col-sm-12 pad0">
                                            <label class="pull-right label <?php echo $action_class ?>" id="action_class_<?php echo $bundle['id'] ?>">
                                                <?php echo $action ?>
                                            </label>
                                        </div>
                                    </div>
                                    <?php if(in_array($this->__access['edit'], $this->__bundle_privilege) || in_array($this->__access['delete'], $this->__bundle_privilege)): ?>
                                    <div class="td-dropdown rTableCell">
                                
                                        <div class="btn-group lecture-control">
                                            <span class="dropdown-tigger" data-toggle="dropdown">
                                                <span class='label-text'>
                                                <i class="icon icon-down-arrow"></i>
                                                </span>
                                                <span class="tilder"></span>
                                            </span>
                                            <ul class="dropdown-menu pull-right" role="menu" id="course_action_<?php echo $bundle['id'] ?>">
                                                <?php
                                                if($bundle['c_deleted'] == 0){ 
                                                ?>
                                                    <?php if(in_array($this->__access['edit'], $this->__bundle_privilege)): ?>
                                                    <li id="status_btn_<?php echo $bundle['id'] ?>">
                                                        <?php $c_status        = ($bundle['c_status'])?'deactivate':'activate'; ?>
                                                        <?php $language_status = ($bundle['c_status'])?'bundle_deactivate':'bundle_activate'; ?>
                                                        <?php $c_action        = ($bundle['c_status'])?'unpublish':'publish'; ?>
                                                        <a href="javascript:void(0);" onclick="changeBundlestatus('<?php echo $bundle['id'] ?>', '<?php echo $c_status ?>','<?php echo addslashes($bundle['c_title'])?>' )" ><?php echo lang($language_status) ?></a>
                                                    </li>
                                                    
                                                    <li>
                                                        <a href="<?php echo admin_url('bundle/basic/'.$bundle['id']) ?>"><?php echo 'Settings' ?></a>
                                                    </li>
                                                    <?php endif; ?>
                                                    <?php if(in_array($this->__access['delete'], $this->__bundle_privilege)): ?>
                                                    <li>
                                                        <a href="javascript:void(0);" id="delete_btn_<?php echo $bundle['id'] ?>" onclick="deleteBundle('<?php echo $bundle['id'] ?>','<?php echo $change_status; ?>', '<?php echo addslashes($bundle['c_title']) ?>')" ><?php echo lang('delete') ?></a>
                                                    </li>
                                                    <?php endif; ?>
                                                <?php
                                                }else{
                                                ?>
                                                    <?php if(in_array($this->__access['edit'], $this->__bundle_privilege)): ?>
                                                    <li>
                                                        <a href="javascript:void(0);" id="restore_btn_<?php echo $bundle['id'] ?>" onclick="restoreBundle('<?php echo $bundle['id'] ?>', '<?php echo addslashes($bundle['c_title']) ?>')" ><?php echo lang('restore') ?></a>
                                                    </li>
                                                    <?php endif; ?>
                                                <?php
                                                }
                                                ?>      
                                            </ul>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                </div>    
                            <?php 
                            } 
                            ?>
                        <?php
                        }else{
                        ?>
                            <div id="popUpMessagePage" class="alert alert-danger">
                                <?php echo lang('no_bundles_found') ?>    
                            </div>    
                        <?php 
                        } 
                        ?>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 text-center">
                            <a id="loadmorebutton" <?php echo ((!$show_load_button)?'style="display:none;"':'') ?>  class="btn btn-green selected" onclick="loadMoreBundle()">Load More <?php echo $remaining_bundle ?></a>
                        </div>
                    </div>
                </div>
            </div>     

            <!-- =========================== -->
            <!-- Content Section --> <!-- END -->

        </div>
        <!-- ==========================  -->
        <!--  LEFT CONTENT--> <!-- ENDS -->


    </section>

    

<script type="text/javascript" src="<?php echo assets_url() ?>js/bundle.js"></script>
<?php include_once 'footer.php';?>

<script type="text/javascript">
    
    var __limit         = <?php echo isset($limit)?$limit:10; ?>;
    var __offset        = 2;
    var __totalUsers    = <?php echo $total_bundles; ?>;
    var __shownUsers    = <?php echo sizeof($bundles); ?>;

</script>
<div class="modal fade" data-backdrop="static" data-keyboard="false" id="create_bundle" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-small" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon icon-cancel-1"></span></button>
                    <h4 class="modal-title" id="create_box_title"><?php echo lang('create_new_bundle'); ?></h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label><?php echo lang('bundle_code'); ?></label>
                        <input type="text" maxlength="5" id="bundle_code" placeholder="eg: MA44" class="form-control">
                    </div>
                    <div class="form-group">
                        <label><?php echo lang('bundle_name'); ?></label>
                        <input type="text" maxlength="50" id="bundle_name" placeholder="eg: Physics Edition" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-red" data-dismiss="modal" ><?php echo lang('cancel') ?></button>
                    <button type="button" class="btn btn-green" onclick="createBundleConfirmed()" id="create_box_ok"><?php echo lang('create') ?></button>
                </div>
            </div>
        </div>
    </div>
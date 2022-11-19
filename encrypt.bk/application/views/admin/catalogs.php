<?php include_once 'header.php';?>
<?php $actions = config_item('actions'); ?>        
    
    <!-- MAIN TAB --> <!-- STARTS -->
    <section class="courses-tab base-cont-top">
        <ol class="nav nav-tabs offa-tab">
            <!-- active tab start -->
            <li>
                <a href="<?php echo admin_url('course') ?>"><?php echo lang('courses') ?></a>
                <span class="active-arrow"></span>
            </li>
            <!-- active tab end -->
            <li class="active">
                <a href="javascript:void(0)"><?php echo lang('catalogs') ?></a>
                <span class="active-arrow"></span>
            </li>
        </ol>
    </section>
    <!-- MAIN TAB --> <!-- END -->

    <!-- SIDEBAR RIGHT SIDE FIXED --> <!-- START -->
    <div class="right-wrap base-cont-top container-fluid pull-right">
        <a href="javascript:void(0)" class="btn btn-green btn-big full-width-btn" data-toggle="modal" data-target="#create-catalog-new" onclick="createCatalog();">
            <i class="icon icon-graduation-cap icon-center"></i>
            <?php echo lang('create_new_catalog') ?>
        </a>

            <!--  Adding list group  --> <!-- START  -->
            
            <h4><?php echo lang('catalog') ?> ?</h4>

            <div class="row">
                <div class="col-sm-12">
                 <?php echo lang('catalog_help') ?>                
                </div>
            </div>          
    
            <!--  Adding list group  --> <!-- END  -->

    </div>
    <!-- SIDEBAR RIGHT SIDE FIXED --> <!-- END -->


    <section class="content-wrap base-cont-top">

        <!-- Nav section inside this wrap  --> <!-- START -->
            <!-- =========================== -->

            <div class="container-fluid nav-content">

                <div class="row">
                    <div class="rTable content-nav-tbl" style="">
                        <div class="rTableRow">

                            <div class="rTableCell">
                                <a href="javascript:void(0)" class="select-all-style"><label> <input class="catalog-checkbox-parent" type="checkbox"><?php  echo lang('select_all') ?></label><span id="selected_catalog_count"></span></a>

                            </div>
                            <div class="rTableCell dropdown">

                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" id="filter_dropdown_text"> All <span class="caret"></span></a>
                                    <ul class="dropdown-menu white">
                                        <li><a href="javascript:void(0)" id="filer_dropdown_list_all" onclick="filter_catalog_by('all')"><?php echo lang('all_catalogs') ?></a></li>
                                        <li><a href="javascript:void(0)" id="filer_dropdown_list_inactive" onclick="filter_catalog_by('inactive')"><?php echo lang('inactive_catalogs') ?></a></li>
                                        <li><a href="javascript:void(0)" id="filer_dropdown_list_active" onclick="filter_catalog_by('active')"><?php echo lang('active_catalogs') ?></a></li>
                                        <li><a href="javascript:void(0)" id="filer_dropdown_list_deleted" onclick="filter_catalog_by('deleted')"><?php echo lang('deleted_catalogs') ?></a></li>
                                    </ul>

                            </div>

                            <div class="rTableCell dropdown challenge-zone-drop">

                                <a href="#" class="dropdown-toggle filter-category-dd" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" id="dropdown_text"> All <?php echo lang('category') ?> <span class="caret"></span></a>
                                    <ul class="dropdown-menu white">
                                        <li><a href="javascript:void(0)" id="dropdown_list_all" onclick="filter_category('all')">All <?php echo lang('category') ?> </a></li>
                                        <li><a href="javascript:void(0)" id="dropdown_list_uncategorised" onclick="filter_category('uncategorised')"><?php echo lang('uncategorized') ?> </a></li>
                                        <?php if(!empty($categories)): ?>
                                        <?php foreach($categories as $category): ?>
                                            <?php if(strip_tags($category['ct_name'])): ?>
                                                <li><a href="javascript:void(0)" id="dropdown_list_<?php echo $category['id'] ?>" onclick="filter_category(<?php echo $category['id'] ?>)"><?php echo htmlentities($category['ct_name']) ?></a></li>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                        <?php endif; ?>
                                    </ul>

                            </div>

                            <div class="rTableCell">
                                <div class="input-group">
                                    <input type="text" class="form-control srch_txt" id="catalog_keyword" placeholder="<?php echo lang('search_by_name') ?>" />
                                    <a class="input-group-addon" id="basic-addon2">
                                        <i class="icon icon-search"> </i>
                                    </a>
                                </div> 
                            </div>

                            <div class="rTableCell" >
                                <!-- lecture-control start -->
                                <div class="btn-group lecture-control btn-right-align" style="margin-top: 0px; display:none;" id="catalog_bulk">
                                    <span class="dropdown-tigger" data-toggle="dropdown">
                                        <span class='label-text'>
                                           Bulk Action  <!-- <span class="icon icon-down-arrow"></span> -->
                                        </span>
                                        <span class="tilder"></span>
                                    </span>
                                    <ul class="dropdown-menu pull-right" role="menu">
                                        <li><a href="javascript:void(0)" class="list-disabled list-button" onclick="changeStatusBulk('<?php echo lang('are_you_sure_to').' '.lang('activate_bulk_catalog').' ?' ?>', '1', '<?php echo lang('activate') ?>')"><?php echo lang('activate') ?> </a></li>
                                        <li><a href="javascript:void(0)" class="list-disabled list-button" onclick="changeStatusBulk('<?php echo lang('are_you_sure_to').' '.lang('deactivate_bulk_catalog').' ?' ?>', '0', '<?php echo lang('deactivate') ?>')"><?php echo lang('deactivate') ?> </a></li>
                                    </ul>
                                </div>
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

        <div class="left-wrap col-sm-12">

            <!-- Content Section --> <!-- START -->
            <!-- =========================== -->
            <div class="row">   
                <div class="col-sm-12 course-cont-wrap wrap-fix-course" id="show_message_div"> 
                    <div class="table course-cont only-course rTable" style="" id="catalog_row_wrapper">
                        <?php if(!empty($catalogs)): ?>
                                <?php //echo "<pre>"; print_r($catalogs); die;?>
                            <?php foreach ($catalogs as $catalog): ?>
                         <div class="rTableRow" id="catalog_row_<?php echo $catalog['id'] ?>" data-title="<?php echo $catalog['c_title'] ?>" data-price="<?php echo $catalog['c_price'] ?>">
                            
                            <?php 
                            //set the database value
                            $action_label   = $catalog['wa_name'];
                            $action         = $catalog['wa_name'];
                            $action_date    = date("d M Y", strtotime($catalog['updated_date']));
                            $action_author  = ($catalog['us_name']!='')?$catalog['us_name']:'Admin';

                            //consider the record is deleted and set the value if record deleted
                            $label_class    = 'spn-delete';
                            $action_class   = 'label-danger';
                            $item_deleted   = 'item-deleted';
                            //case if record is not deleted
                            if($catalog['c_deleted'] == 0)
                            {
                                $item_deleted   = '';
                                if($catalog['action_id'] == 1)
                                {
                                    $action_class   = 'label-warning';                            
                                    $action_date    = date("d M Y", strtotime($catalog['created_date']));
                                    $label_class    = 'spn-inactive';
                                }
                                else
                                {
                                    if($catalog['c_status'] == 1)
                                    {
                                        $action_class   = 'label-success';                                                                
                                        $label_class    = 'spn-active';                                        
                                        $action         = lang('active');
                                    }
                                    else
                                    {
                                        $action_class   = 'label-warning';                                                                
                                        $label_class    = 'spn-inactive';                                        
                                        $action         = lang('inactive');
                                    }
                                }
                            }
                            ?>
                            <div class="rTableCell cours-fix ellipsis-hidden"> 
                                <div class="ellipsis-style">  
                                    <input type="checkbox" class="catalog-checkbox <?php echo $item_deleted ?>" value="<?php echo $catalog['id'] ?>" id="catalog_details_<?php echo $catalog['id'] ?>"> 
                                    <span class="icon-wrap-round">
                                        <i class="icon icon-graduation-cap"></i>
                                    </span>
                                    <a href="<?php echo admin_url('catalog_settings/basics/').$catalog['id'] ?>" class="cust-sm-6 padd0"> <?php echo $catalog['c_title'] ?></a>
                                </div>
                            </div>
                            <div class="rTableCell pad0 cours-fix width70"> 
                                <div class="col-sm-12 pad0">
                                    <label class="pull-right label <?php echo $action_class ?>" id="action_class_<?php echo $catalog['id'] ?>">
                                        <?php echo $action ?>
                                    </label>
                                </div>
                                <div class="col-sm-12 pad0 pad-vert5 pos-inhrt">   
                                    <span class="pull-right <?php echo $label_class ?>" id="label_class_<?php echo $catalog['id'] ?>"> <?php echo $action_label ?> by- <?php echo $action_author ?> on <?php echo $action_date ?></span>
                                </div>
                            </div>
                            <div class="td-dropdown rTableCell">
                                <div class="btn-group lecture-control">
                                    <span class="dropdown-tigger" data-toggle="dropdown">
                                         <span class='label-text'>
                                          <i class="icon icon-down-arrow"></i>
                                        </span>
                                        <span class="tilder"></span>
                                    </span>
                                    <ul class="dropdown-menu pull-right" role="menu" id="catalog_action_<?php echo $catalog['id'] ?>">
                                        <?php if($catalog['c_deleted'] == 0): ?>
                                            <li id="status_btn_<?php echo $catalog['id'] ?>">
                                                <?php $cb_status = ($catalog['c_status'])?'deactivate':'activate'; ?>
                                                <?php $cb_action = $cb_status; ?>
                                                <a href="javascript:void(0);" data-toggle="modal" onclick="changeCatalogStatus('<?php echo $catalog['id'] ?>', '<?php echo lang('are_you_sure_to').' '.lang($cb_action).' '.  lang('catalog').' - '.addslashes($catalog['c_title']).' ?' ?>', '<?php echo lang($cb_action) ?>', '<?php echo lang($cb_status) ?>')" data-target="#publish-course"><?php echo lang($cb_status) ?></a>
                                            </li>
                                            <li>
                                                <a href="<?php echo admin_url('catalog_settings/basics/').$catalog['id'] ?>"><?php echo lang('settings') ?></a>
                                            </li>
                                            <li>
                                                <a href="javascript:void(0);" id="delete_btn_<?php echo $catalog['id'] ?>" data-toggle="modal" onclick="deleteCatalog('<?php echo $catalog['id'] ?>', '<?php echo lang('are_you_sure_to').' '.lang('delete_catalog').' '.addslashes($catalog['c_title']).' ?' ?>')" data-target="#publish-course"><?php echo lang('delete') ?></a>
                                            </li>
                                        <?php else: ?>
                                            <li>
                                                <a href="javascript:void(0);" id="restore_btn_<?php echo $catalog['id'] ?>" data-toggle="modal" onclick="restoreCatalog('<?php echo $catalog['id'] ?>', '<?php echo lang('are_you_sure_to').' '.lang('restore_catalog').' '.addslashes($catalog['c_title']).' ?' ?>')" data-target="#publish-course"><?php echo lang('restore') ?></a>
                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>    
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="rTableRow">
                            <div class="rTableCell cours-fix ellipsis-hidden"> 
                                <div class="ellipsis-style">  
                                    <a href="javascript:void(0)" class="cust-sm-6 padd0"> <?php echo lang('no_catalogs_found') ?></a>
                                </div>
                            </div>
                        </div>    
                        <?php endif; ?>
                    </div>
                </div>
            </div>     

            <!-- =========================== -->
            <!-- Content Section --> <!-- END -->

        </div>
        <!-- ==========================  -->
        <!--  LEFT CONTENT--> <!-- ENDS -->


    </section>
<script type="text/javascript" src="<?php echo assets_url() ?>js/catalog.js"></script>
<?php include_once 'footer.php';?>
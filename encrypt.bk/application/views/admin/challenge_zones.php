<?php //echo "<pre>";print_r($challenges);die; ?>
<?php include_once 'header.php';?>
<link rel="stylesheet" href="<?php echo assets_url() ?>css/datepicker.min.css">
<link rel="stylesheet" href="<?php echo assets_url() ?>css/timepicker.css">
<?php $actions = config_item('actions'); ?>        
    
    <?php //include_once "cms_tab.php"; ?>
<section class="courses-tab base-cont-top"> 
    <ol class="nav nav-tabs offa-tab">
        <li class="active">
            <a href="javascript:void(0)"> Challenge Zone</a>
            <span class="active-arrow" style="background: rgb(255, 255, 255) none repeat scroll 0% 0%;"></span>
        </li>
    </ol>
</section>


    <!-- SIDEBAR RIGHT SIDE FIXED --> <!-- START -->
    <div class="right-wrap base-cont-top container-fluid pull-right">
        <a href="javascript:void(0)" class="btn btn-green btn-big full-width-btn" data-toggle="modal" data-target="#challenge_zone">
            <i class="icon icon-graduation-cap icon-center"></i>
            <?php echo lang('create_new_challenge') ?>
        </a>

            <!--  Adding list group  --> <!-- START  -->
            
            <h4><?php echo lang('challenge_zones') ?> ?</h4>

            <div class="row">
                <div class="col-sm-12">
                 <?php echo lang('challenge_help') ?>                
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
                                <a href="javascript:void(0)" class="select-all-style"><label> <input class="challenge-checkbox-parent" type="checkbox"><?php  echo lang('select_all') ?></label><span id="selected_challenge_count"></span></a>

                            </div>

                            <div class="rTableCell dropdown challenge-zone-drop">

                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" id="dropdown_text"> All <?php echo lang('category') ?> <span class="caret"></span></a>
                                    <ul class="dropdown-menu white">
                                        <li><a href="javascript:void(0)" id="dropdown_list_all" onclick="filter_category('all')">All <?php echo lang('category') ?> </a></li>
                                        <!--<li><a href="javascript:void(0)" id="dropdown_list_uncategorised" onclick="filter_category('uncategorised')"><?php //echo lang('uncategorized') ?> </a></li>-->
                                        <?php if(!empty($categories)): ?>
                                        <?php foreach($categories as $category): ?>
                                            <?php if(strip_tags($category['ct_name'])): ?>
                                            <li><a href="javascript:void(0)" id="dropdown_list_<?php echo $category['id'] ?>" onclick="filter_category(<?php echo $category['id'] ?>)"><?php echo $category['ct_name'] ?></a></li>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                        <?php endif; ?>
                                    </ul>

                            </div>
                            <div class="rTableCell" ></div>
                            <div class="rTableCell" >
                                <!-- lecture-control start -->
                                <div class="btn-group lecture-control btn-right-align" style="margin-top: 0px; display:none;" id="challenge_bulk">
                                    <span class="dropdown-tigger" data-toggle="dropdown">
                                        <span class='label-text'>
                                           Bulk Action  <!-- <span class="icon icon-down-arrow"></span> -->
                                        </span>
                                        <span class="tilder"></span>
                                    </span>
                                    <ul class="dropdown-menu pull-right" role="menu">
                                        <li>
                                            <a href="javascript:void(0)" onclick="deleteChallengeZoneBulk('<?php echo lang('are_you_sure_to').' '.lang('delete_selected_challenges').'?' ?>', '1')" data-target="#challenge_delete" ><?php echo lang('delete_challenges') ?></a>
                                        </li>
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
                    <div class="table course-cont only-course rTable" style="" id="challenge_row_wrapper">
                        <?php if(!empty($challenges)): ?>
                            <?php foreach ($challenges as $challenge): ?>
                                
                                <?php
                                    //set the database value
                                    $action_label = $challenge['wa_name'];
                                    $action = $challenge['wa_name'];
                                    $action_date = date("d M Y", strtotime($challenge['updated_date']));
                                    $action_author = ($challenge['wa_name_author'] != '') ? $challenge['wa_name_author'] : 'Admin';

                                    //consider the record is deleted and set the value if record deleted
                                    $label_class = 'spn-delete';
                                    $action_class = 'label-danger';
                                    $item_deleted   = 'item-deleted';
                                    $item_inactive  = '' ;
                                    //case if record is not deleted
                                    if ($challenge['cz_deleted'] == 0) {
                                        $item_deleted = '';
                                        if ($challenge['action_id'] == 1) {
                                            $action_class = 'label-warning';
                                            $action_date = date("d M Y", strtotime($challenge['created_date']));
                                            $label_class = 'spn-inactive';
                                            $item_inactive    = 'item_inactive';
                                        } else {
                                            if ($challenge['cz_status'] == 1) {
                                                $action_class = 'label-success';
                                                $label_class = 'spn-active';
                                                $action = lang('active');
                                            } else {
                                                $action_class = 'label-warning';
                                                $label_class = 'spn-inactive';
                                                $action = lang('inactive');
                                                $item_inactive    = 'item_inactive';
                                            }
                                        }
                                    }
                                ?>
                        
                        
                        
                         <div class="rTableRow" id="challenge_row_<?php echo $challenge['id'] ?>" data-title="<?php //echo $challenge['cz_title'] ?>">
                            <div class="rTableCell cours-fix ellipsis-hidden"> 
                                <div class="ellipsis-style">  
                                    <input type="checkbox" class="challenge-checkbox" value="<?php echo $challenge['id'] ?>" id="challenge_details_<?php echo $challenge['id'] ?>"> 
                                    <span class="icon-wrap-round">
                                        <small class="icon-custom"><?php echo strtoupper(trim(substr($challenge['cz_title'], 0, 1))); ?></small>
                                    </span>
                                    <a href="<?php echo admin_url('challenge_zone/basics/'.$challenge['id']) ?>" class="cust-sm-6 padd0"> <?php echo $challenge['cz_title'] ?></a>
                                </div>
                            </div>
                            
                            <div class="rTableCell pad0 cours-fix width70"> 
                                <div class="col-sm-12 pad0" id="activate_challenge_label_<?php echo $challenge['id'] ?>">
                                    <label class="pull-right label <?php echo $action_class ?>" id="action_class_<?php echo $challenge['id'] ?>">
                                        <?php echo $action ?>
                                    </label>
                                </div>
                                <div class="col-sm-12 pad0 pad-vert5 pos-inhrt" id="activate_challenge_modified_<?php echo $challenge['id'] ?>">   
                                    <span class="pull-right <?php echo $label_class ?>" id="label_class_<?php echo $challenge['id'] ?>"> <?php echo $action_label ?> by- <?php echo $action_author ?> on <?php echo $action_date ?></span>   
                                </div>
                            </div>


                             <div class="td-dropdown rTableCell">
                                <div class="btn-group lecture-control">
                                    <span class="dropdown-tigger" data-toggle="dropdown">
                                         <span class="label-text">
                                          <i class="icon icon-down-arrow"></i>
                                        </span>
                                        <span class="tilder"></span>
                                    </span>
                                    <ul class="dropdown-menu pull-right" role="menu" id="challenge_action_<?php echo $challenge['id'] ?>">
                                        <?php if($challenge['cz_deleted'] == 0): ?>
                                            <li>
                                                <a href="<?php echo admin_url('challenge_zone/report/'.$challenge['id']); ?>" id="report_btn_<?php echo $challenge['id'] ?>" ><?php echo lang('report') ?></a>
                                            </li>
                                            <li>
                                                <a href="<?php echo admin_url('challenge_zone/basics/'.$challenge['id']) ?>" id="settings_btn_<?php echo $challenge['id'] ?>"><?php echo lang('settings') ?></a>
                                            </li>
                                            <?php if($challenge['cz_status'] == '0'): ?>
                                            <li id="activate_challenge_<?php echo $challenge['id'] ?>">
                                                <a href="javascript:void(0);" id="activate_btn_<?php echo $challenge['id'] ?>" data-toggle="modal" onclick="activateChallenge('<?php echo $challenge['id'] ?>', '<?php echo base64_encode(lang('are_you_sure_to').' '.lang('activate_challenge').' - '.addslashes($challenge['cz_title']).' ?') ?>')" data-target="#challenge_delete"><?php echo lang('activate') ?></a>
                                            </li>
                                            <?php endif; ?>
                                            <li>
                                                <a href="javascript:void(0);" id="delete_btn_<?php echo $challenge['id'] ?>" data-toggle="modal" onclick="deleteChallenge('<?php echo $challenge['id'] ?>', '<?php echo base64_encode(lang('are_you_sure_to').' '.lang('delete_challenge').' - '.addslashes($challenge['cz_title']).' ?') ?>')" data-target="#challenge_delete"><?php echo lang('delete') ?></a>
                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                            </div>
                             
                             
                             
                             
                        </div>    
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div id="popUpMessage" class="alert alert-danger">
                                <a data-dismiss="alert" class="close">×</a>
                                <?php echo lang('no_challenges_found') ?>
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

<script type="text/javascript" src="<?php echo assets_url() ?>js/datepicker.js"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/datepicker.en.js"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/jquery.timepicker.js"></script>

<script type="text/javascript">
var challenge_id = 0;
var __start_pop_time        = '';
var __end_pop_time          = '';
var __start_time        = '';
var __end_time          = '';

</script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/challenge_zone.js"></script>
<?php include_once 'footer.php';?>
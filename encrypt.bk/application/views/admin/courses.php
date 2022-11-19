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
$admins = array();
$sub_admins = $this->config->item('sub_admins');
if(!($sub_admins))
{
    foreach ($sub_admins as $key => $value) {
        $admins[$key] = $value;
    }    
}
$admins[count($admins)] = $this->config->item('super_admin');
//echo '<pre>';print_r($admins);die;
?>
    <?php 
    if(in_array('2', $course_privilege)){
    ?>
    <div class="right-wrap base-cont-top container-fluid pull-right">
        <a href="javascript:void(0)" id="course_create" class="btn btn-green btn-big full-width-btn" onclick="createCourse('<?php echo lang('create_new_course') ?>', '<?php echo lang('course_title') ?>*:');">
            <?php echo lang('create_new_course') ?>
        </a>

    </div>
    <?php 
    }
    ?>
    <!-- SIDEBAR RIGHT SIDE FIXED --> <!-- END -->

    <?php 
        
        $wrapper_class = '';
        if(!in_array('2', $course_privilege))
        {
            $wrapper_class = 'content-wrap1';
        }
        ?>
    <section class="content-wrap <?php echo $wrapper_class ?> base-cont-top">

        <!-- Nav section inside this wrap  --> <!-- START -->
            <!-- =========================== -->

            <div class="container-fluid nav-content nav-course-content">

                <div class="row">
                    <div class="rTable content-nav-tbl" style="">
                        <div class="rTableRow d-flex">

                            <div class="rTableCell" style="width: 170px" >
                                <a href="javascript:void(0)" class="select-all-style"><label> 
                                <input class="course-checkbox-parent" type="checkbox"><?php  echo lang('select_all') ?><span id="selected_course_count"></span></label></a>

                            </div>
                            <div class="rTableCell dropdown" style="width: 250px">

                                <a href="#" class="dropdown-toggle"  data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" id="filter_dropdown_text"><?php echo lang('active_courses') ?><span class="caret"></span></a>
                                    <ul class="dropdown-menu white">
                                        <li><a href="javascript:void(0)" id="filer_dropdown_list_all" onclick="filter_course_by('all')"><?php echo lang('all_courses') ?></a></li>
                                        <li><a href="javascript:void(0)" id="filer_dropdown_list_inactive" onclick="filter_course_by('inactive')"><?php echo lang('inactive_courses') ?></a></li>
                                        <li><a href="javascript:void(0)" id="filer_dropdown_list_active" onclick="filter_course_by('active')"><?php echo lang('active_courses') ?></a></li>
                                        <li><a href="javascript:void(0)" id="filer_dropdown_list_deleted" onclick="filter_course_by('deleted')"><?php echo lang('deleted_courses') ?></a></li>
                                    </ul>

                            </div>

                            <div class="rTableCell dropdown challenge-zone-drop" style="width: 250px">

                                <a href="#" class="dropdown-toggle"   data-toggle="dropdown" role="button" title="" aria-haspopup="true" aria-expanded="false" id="dropdown_text"><span title="All Category" class="category-text-ellipsis" > All <?php echo lang('category') ?></span>
                                    <span class="caret" style="margin-top: -8px;"></span>
                                </a>
                                            
                                    <ul class="dropdown-menu white" style="min-width: 225px !important; width:auto;">
                                        <li><a href="javascript:void(0)" id="dropdown_list_all" onclick="filter_category('all')">All <?php echo lang('category') ?></a></li>
                                        <!-- <li><a href="javascript:void(0)" id="dropdown_list_uncategorised" onclick="filter_category('uncategorised')"><?php //echo lang('uncategorized') ?> </a></li> -->
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
                                <div class="btn-group lecture-control m-0" style="display:none;" id="course_bulk">
                                    <span class="dropdown-tigger" data-toggle="dropdown" style="padding-top: 2px;">
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
                    <div >
                        <div class="pull-right">
                            <!-- Header left items -->
                            <h4 class="right-top-header course-count">
                                <?php 
                                $course_html  = '';
                                if($total_courses < 1) {
                                    $course_html = 'No Courses';
                                } else {
                                    $course_html .= sizeof($courses).' / '.$total_courses;
                                    $course_html .= ($total_courses>1)?' Courses':' Course';    
                                }
                                echo $course_html;
                                $remaining_course = $total_courses - sizeof($courses);
                                $remaining_course = ($remaining_course>0)?'('.$remaining_course.')':'';
                                ?>
                            </h4>
                        </div>
                        <!-- !.Header left items -->
                    </div>
                    <div class="table course-cont only-course rTable" style="" id="course_row_wrapper">
                        <?php if(!empty($courses)): ?>
                            <?php foreach ($courses as $course): ?>
                         <div class="rTableRow" id="course_row_<?php echo $course['id'] ?>" data-title="<?php echo addslashes($course['cb_title']) ?>" >
                            
                            <?php 
                            //consider the record is deleted and set the value if record deleted
                            $label_class    = 'spn-delete';
                            $action_class   = 'label-danger';
                            //case if record is not deleted
                            $item_deleted   = 'item-deleted';
                            $action         = lang('deleted');
                            $item_inactive  = '' ;
                            if($course['cb_deleted'] == 0)
                            {
                                $item_deleted = '';
                               
                                    switch($course['cb_status'])
                                    {
                                        case 1:
                                            $action_class   = 'label-success';
                                            $label_class    = 'spn-active';                                        
                                            $action         = lang('active');
                                        break;
                                        case 2:
                                            $action_class   = 'label-warning';
                                            $item_inactive  = 'item_inactive';                               
                                            $label_class    = 'spn-inactive';                                        
                                            $action   = lang('pending_approval');
                                        break;
                                        default :
                                            $action_class   = 'label-warning';
                                            $item_inactive  = 'item_inactive';                                 
                                            $label_class    = 'spn-inactive';                                        
                                            $action         = lang('inactive');
                                        break;
                                    }
                            }
                            ?>
                            <div class="rTableCell cours-fix ellipsis-hidden"> 
                                <div class="ellipsis-style display-initial">  
                                <?php 
                                    $is_disabled = ($course['cb_deleted'] == 1)? 'disabled="disabled"' : '';
                                 ?>
                                    <input type="checkbox" <?php echo $is_disabled; ?> class="course-checkbox <?php echo $item_deleted.' '.$item_inactive ?>" value="<?php echo $course['id'] ?>" id="course_details_<?php echo $course['id'] ?>"> 
                                    
                                    <?php if($course['cb_deleted'] != 1): ?>
                                        <a href="<?php echo admin_url('course/basic/'.$course['id']) ?>" class="cust-sm-6 padd0">
                                    <?php endif; ?>
                                    <span class="icon-wrap-round color-box" data-name="<?php echo $course['cb_title']; ?>">
                                        <i class="icon icon-graduation-cap"></i>
                                    </span><span class="institution-code">
                                    <?php echo strtoupper( $course['cb_code'] ).'</span> - '; ?> <?php echo $course['cb_title'] ?>
                                    <?php if($course['cb_deleted'] != 1): ?>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php
                                $cb_date='';
                                if($course['cb_access_validity'] == 0){
                                    $cb_date = '<span class="text-green">Unlimited</span>';
                                }else if($course['cb_access_validity'] == 1){
                                    $cb_date = '<span class="text-green">'.$course['cb_validity']." days</span>";
                                }else if($course['cb_access_validity'] == 2){

                                    $current_date   = strtotime(date("d-m-Y",time())); 
                                    $db_date        = strtotime($course['cb_validity_date']);
                                    if($current_date > $db_date){
                                        $cb_date = '<span class="text-red">Expired</span>';
                                    }else if($current_date==$db_date){
                                        $cb_date = '<span class="text-orange">Today</span>';
                                    }else{
                                        $cb_date ='<span class="text-green">'.date("d-m-Y",$db_date).'</span>';
                                    }
                                }
                            ?>
                            <div class="rTableCell text-center ">
                                <?php echo $cb_date; ?>
                            </div> 
                             
                            <div class="rTableCell pad0 cours-fix width70"> 
                                <div class="col-sm-12 pad0">
                                    <label class="pull-right label <?php echo $action_class ?>" id="action_class_<?php echo $course['id'] ?>">
                                        <?php echo $action ?>
                                    </label>
                                </div>
                            </div>
                            <div class="td-dropdown rTableCell">
                            <?php 
                            if(in_array('3', $course_privilege)||in_array('4', $course_privilege)){
                            ?>
                                <div class="btn-group lecture-control">
                                    <span class="dropdown-tigger" data-toggle="dropdown">
                                         <span class='label-text'>
                                          <i class="icon icon-down-arrow"></i>
                                        </span>
                                        <span class="tilder"></span>
                                    </span>
                                    <ul class="dropdown-menu pull-right" role="menu" id="course_action_<?php echo $course['id'] ?>">
                                        <?php if($course['cb_deleted'] == 0): ?>
                                        
                                        <?php if(in_array('3', $course_privilege)):
                                            ?>
                                            <li id="status_btn_<?php echo $course['id'] ?>">
                                                <?php $cb_status = ($course['cb_status'])?'deactivate':'activate'; ?>
                                                <?php $cb_action = ($course['cb_status'])?'unpublish':'publish'; ?>
                                                <a href="javascript:void(0);" onclick="changeCourseStatus('<?php echo $course['id'] ?>', '<?php echo $cb_status ?>','<?php echo addslashes($course['cb_title'])?>' )" ><?php echo lang($cb_status) ?></a>
                                            </li>
                                            
                                            <li>
                                                <a href="<?php echo admin_url('course_settings/basics/'.$course['id']) ?>"><?php echo 'Settings' ?></a>
                                            </li>
                                            <?php
                                            endif; 
                                            if(in_array('4', $course_privilege)):
                                            ?>
                                            <li>
                                                <a href="javascript:void(0);" id="delete_btn_<?php echo $course['id'] ?>" onclick="deleteCourse('<?php echo $course['id'] ?>', '<?php echo addslashes($course['cb_title']) ?>')" ><?php echo lang('delete') ?></a>
                                            </li>
                                            <?php
                                            endif;
                                        ?>
                                        <?php else: 
                                                if(in_array('4', $course_privilege)):?>
                                                <li>
                                                    <a href="javascript:void(0);" id="restore_btn_<?php echo $course['id'] ?>" onclick="restoreCourse('<?php echo $course['id'] ?>', '<?php echo addslashes($course['cb_title']) ?>')" ><?php echo lang('restore') ?></a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0);" id="restore_btn_<?php echo $course['id'] ?>" onclick="deleteCoursePermanently('<?php echo $course['id'];?>', '<?php echo addslashes($course['cb_title']);?>','<?php echo $course['id'];?>')" >Delete Permanently</a>
                                                </li>
                                                <?php   
                                                endif;
                                            endif; ?>
                                    </ul>
                                </div>
                            <?php
                            }
                            ?>
                            </div>
                        </div>    
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div id="popUpMessagePage" class="alert alert-danger">
                                <?php echo lang('no_courses_found') ?>    
                            </div>    
                        <?php endif; ?>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 text-center">
                            <a id="loadmorebutton" <?php echo ((!$show_load_button)?'style="display:none;"':'') ?>  class="btn btn-green selected" onclick="loadMoreCourse()">Load More <?php echo $remaining_course ?></a>
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

<script type="text/javascript" src="<?php echo assets_url() ?>js/course.js"></script>
<?php include_once 'footer.php';?>

<script type="text/javascript">
    var __admins = '<?php echo json_encode($admins); ?>';
    __admins = $.parseJSON(__admins);
    var __limit  = <?php echo isset($limit)?$limit:10; ?>;
    var __offset = 2;
    var __totalUsers    = <?php echo $total_courses; ?>;
    var __shownUsers    = <?php echo sizeof($courses); ?>;
    //console.log(__admins);
    var course_permissions = '<?php echo json_encode($course_privilege); ?>';
    var __course_privilege = new User(course_permissions);

//       $(document).ready(function(e) {
//          $('.color-box').initial_with_icon({width:40,height:40,fontSize:20,fontWeight:400});
//     $('#course_row_wrapper').bind('DOMSubtreeModified', function(e) {
//         $('.color-box').initial_with_icon({width:40,height:40,fontSize:20,fontWeight:400});
//     });
// });
</script>
<?php include_once 'header.php';?>
<?php $actions = config_item('actions'); ?>
<?php
// $admins = array();
// $sub_admins = $this->config->item('sub_admins');
// foreach ($sub_admins as $key => $value) {
//     $admins[$key] = $value;
// }
// $admins[count($admins)] = $this->config->item('super_admin');
//echo '<pre>';print_r($admins);die;
?>

<div class="right-wrap base-cont-top container-fluid pull-right">
        <a href="javascript:void(0)" class="btn btn-green btn-big full-width-btn" data-toggle="modal" data-target="#create_role" onclick="createRole('<?php echo lang('create_new_role') ?>', '<?php echo lang('role_title') ?>*:');">
                <?php echo lang('create_new_role') ?>
        </a>


    </div>
    <!-- SIDEBAR RIGHT SIDE FIXED --> <!-- END -->

    <section class="content-wrap base-cont-top course-content-wrap">        
        <div class="container-fluid nav-content nav-course-content">
            <div class="row">
            <div class="rTable content-nav-tbl" style="">
                <div class="rTableRow"></div>
                <p class="role-title text-left no-margin">Manage Roles</p>
            </div>

            </div>
        </div>

        <div class="left-wrap col-sm-12">
            <div class="row">   
                <div class="col-sm-12 course-cont-wrap wrap-fix-course" id="show_message_div"> 
                    <div class="buldr-header inner-buldr-header question-bank-bulk text-right clearfix row">
                        <div class="pull-right">
                            <!-- Header left items -->
                            <h3 class="right-top-header course-count">
                                <?php 
                                $role_html  = '';
                                $role_html .= sizeof($roles).' / '.$total_roles;
                                $role_html .= ($total_roles>1)?' Roles':' Roles';
                                echo $role_html;
                                // $remaining_course = $total_courses - sizeof($courses);
                                // $remaining_course = ($remaining_course>0)?'('.$remaining_course.')':'';
                                ?>
                            </h3>
                        </div>
                        <!-- !.Header left items -->
                    </div>
                    <div class="table course-cont only-course rTable">
                        <?php if(!empty($roles)): ?>
                            <?php foreach ($roles as $role): ?>
                         <div class="rTableRow" id="role_row_<?php echo $role['id'] ?>" data-title="<?php echo $role['rl_name'] ?>">
                            
                            <?php 

                            //consider the record is deleted and set the value if record deleted
                            $label_class    = 'spn-delete';
                            $action_class   = 'label-danger';
                            //case if record is not deleted
                            $item_deleted   = 'item-deleted';
                            $item_inactive  = '' ;
                            if($role['rl_deleted'] == 0)
                            {
                                $item_deleted = '';
                                    switch($role['rl_status'])
                                    {
                                        case 1:
                                            $action_class   = 'label-success';
                                            $label_class    = 'spn-active';                                        
                                            $action         = lang('active');
                                        break;
                                        default :
                                            $action_class   = 'label-warning';
                                            $item_inactive  = 'item_inactive';                                 
                                            $label_class    = 'spn-inactive';                                        
                                            $action         = lang('inactive');
                                        break;
                                    }
                            } else {
                                $action         = 'Deleted';
                                $action_class   = 'label-danger';
                            }
                            ?>
                            <div class="rTableCell cours-fix ellipsis-hidden"> 
                                <div class="ellipsis-style">  
                                    <!-- <input type="checkbox" class="course-checkbox <?php echo $item_deleted.' '.$item_inactive ?>" value="<?php echo $role['id'] ?>" id="course_details_<?php echo $role['id'] ?>">  -->
                                    <span class="icon-wrap-round">
                                        <i class="icon icon-user"></i>
                                    </span>
                                    <a  href="<?php echo admin_url('role_settings/basics/'.$role['id']) ?>"><?php echo $role['rl_name']; ?></a>
                                </div>
                            </div>
                             
                            <div class="rTableCell pad0 cours-fix width70"> 
                                <div class="col-sm-12 pad0">
                                    <label class="pull-right label <?php echo $action_class ?>" id="action_class_<?php echo $role['id'] ?>">
                                        <?php echo $action ?>
                                    </label>
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
                                    <ul class="dropdown-menu pull-right" role="menu" id="course_action_<?php echo $role['id'] ?>">
                                        <?php if($role['rl_deleted'] == 0): ?>                                        
                                        <li>
                                            <a href="<?php echo admin_url('role_settings/basics/'.$role['id']) ?>">Settings</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0)" data-toggle="modal" data-target="#create_role" onclick="editRole('Edit Role', '<?php echo base64_encode(json_encode($role)); ?>');">Edit</a>                                            
                                        </li>
                                            <?php
                                                if($role['rl_default_role'] == '0'):
                                                ?>
                                                <li>
                                                    <a href="javascript:void(0)" onclick="deleteRole('<?php echo $role['id'] ?>','<?php echo $role['rl_name'] ?>');">Delete</a>                                            
                                                </li>
                                                <?php
                                                endif;
                                            ?>
                                        <?php else: ?>
                                        <li>
                                            <a href="javascript:void(0)" onclick="restoreRole('<?php echo $role['id'] ?>')">Restore</a>                                            
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
                                    <a href="javascript:void(0)" class="cust-sm-6 padd0"> <?php echo lang('no_courses_found') ?></a>
                                </div>
                            </div>
                        </div>    
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        </div>
    </section>

    
<script>
    var __roles     = $.parseJSON(atob("<?php echo base64_encode(json_encode($roles)); ?>"));

$(document).ready(function(){
    //console.log(__roles);
});
</script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/system.js"></script>
    <script type="text/javascript" src="<?php echo assets_url() ?>js/role.js"></script>

<?php include_once 'footer.php';?>




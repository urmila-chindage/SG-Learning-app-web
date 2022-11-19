<?php include_once 'header.php'; ?>
<?php $actions = config_item('actions'); ?>

<!-- MAIN TAB --> <!-- STARTS -->
<section class="courses-tab cont-course-big custom-sidenav" id="courses-tab">
<?php
    $menu_active = array(
                    'overview'=>''
                    ,'basic' => ''
                    , 'users' => ''
                    , 'reviews'    => ''
                    , 'settings_seo' => ''
    );
    $menu_active[$this->router->fetch_method()] = 'active';

?>
    <ol class="nav offa-tab custom-sidemenu">
        <li class="<?php echo $menu_active['overview'] ?>">
            <a href="<?php echo admin_url('bundle/overview/' . $bundle['id']); ?>"><?php echo 'Overview' ?></a>
        </li>
        <!-- active tab start -->
        <li class="<?php echo $menu_active['basic'] ?>">
            <a href="<?php echo admin_url('bundle/basic/' . $bundle['id']); ?>"><?php echo lang('settings') ?></a>
        </li>
        <?php if(in_array($this->__access['view'], $this->__bundle_student_enrollment_privilege)): ?>
        <li class="<?php echo $menu_active['users'] ?>">
            <a href="<?php echo admin_url('bundle/users/' . $bundle['id']); ?>"><?php echo lang('users') ?></a>
        </li>
        <?php endif; ?>
        <li class="<?php echo $menu_active['reviews'] ?>">
            <a href="<?php echo admin_url('bundle/reviews/' . $bundle['id']); ?>"><?php echo lang('reviews') ?></a>
        </li>
       
        
        <li class="<?php echo $menu_active['settings_seo'] ?>">
            <a href="<?php echo admin_url('bundle/seo/' . $bundle['id']); ?>"><?php echo lang('advanced') ?></a>
        </li>

        
    </ol>

</section>
<!-- MAIN TAB --> <!-- END -->

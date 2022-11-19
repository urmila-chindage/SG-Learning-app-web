<?php include_once 'header.php';?>
<section class="courses-tab base-cont-top" id="courses-tab">
    <ol class="nav nav-tabs offa-tab" style="padding-left: 40px;">
        <!-- active tab start -->
        <li class="">
            <a href="<?php echo admin_url('page'); ?>"> Manage Pages</a>
            <span class="active-arrow"></span>
        </li>
        <!-- active tab end -->
        <li class="">
            <a href="<?php echo admin_url('notification'); ?>"> Notifications</a>
            <span class="active-arrow"></span>
        </li>
        <li class="">
            <a href="<?php echo admin_url('termsofday'); ?>"> Term of the Day</a>
            <span class="active-arrow"></span>
        </li>
        <li class="">
            <a href="<?php echo admin_url('challenge_zone'); ?>"> Challenge Zone</a>
            <span class="active-arrow"></span>
        </li>

    </ol>
</section>

<?php include_once 'footer.php';?>
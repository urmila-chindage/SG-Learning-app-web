<aside class="menu-block">
    <ol class="sidebar-menu">
        <?php if($this->auth->is_logged_in(false, false, 'teacher')): ?>
        <li data-toggle="tooltip" title="Dashboard" data-placement="right">
            <a href="<?php echo admin_url('dashboard') ?>">
                <i class="icon icon-gauge"></i>
            </a>
        </li>
        <?php endif; ?>
        <!---->
        <li data-toggle="tooltip" title="Courses" data-placement="right">
            <a href="<?php echo admin_url('course') ?>">
                <i class="icon icon-graduation-cap"></i>
            </a>
        </li>
        <!--<li data-toggle="tooltip" title="Users" data-placement="right">
            <a href="<?php //echo admin_url('user') ?>">
                <i class="icon icon-user"></i>
            </a>
        </li> -->

        <li data-toggle="tooltip" title="Batches" data-placement="right">
            <a href="<?php echo admin_url('groups') ?>">
                <i class="icon icon-users"></i>
            </a>
        </li>
       <!-- <li data-toggle="tooltip" title="Report" data-placement="right">
            <a href="./view-report.html">
                <i class="icon icon-chart-bar"></i>
            </a>
        </li>
        <li data-toggle="tooltip" title="Settings" data-placement="right">
            <a href="#">
                <i class="icon icon-cog-alt"></i>
            </a>
        </li>
        <li data-toggle="tooltip" title="Support" data-placement="right">
            <a href="#">
                <i class="icon icon-lifebuoy"></i>
            </a>
        </li>-->
    </ol>
</aside>

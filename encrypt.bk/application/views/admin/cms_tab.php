<?php 
    $menu_active = array('page' => '', 'notification' => '','event'=>'', 'termsofday' => '', 'challenge_zone' => '', 'expert_lectures' => '', 'survey' => '', 'daily_news' => '', 'generate_test' => '' );
    $menu_active[$this->router->fetch_class()] = 'active';
?>

<!-- MAIN TAB --> <!-- STARTS -->
    <section class="courses-tab base-cont-top"> 
        <ol class="nav nav-tabs offa-tab">
            <!-- active tab start -->
            <li class="<?php echo $menu_active['page'] ?>">
                <a href="<?php echo admin_url('page'); ?>"> Manage Pages</a>
                <span class="active-arrow"></span>
            </li>
            <!-- active tab end -->
            <li class="<?php echo $menu_active['notification'] ?>">
                <a href="<?php echo admin_url('notification'); ?>"> Notifications</a>
                <span class="active-arrow"></span>
            </li>
            <?php /* ?><li class="<?php echo $menu_active['termsofday'] ?>">
                <a href="<?php echo admin_url('termsofday'); ?>"> Term of the Day</a>
                <span class="active-arrow"></span>
            </li>
            <li class="<?php echo $menu_active['challenge_zone'] ?>">
                <a href="<?php echo admin_url('challenge_zone'); ?>"> Challenge Zone</a>
                <span class="active-arrow"></span>
            </li><?php */ ?>
            <li class="<?php echo $menu_active['expert_lectures'] ?>">
                <a href="<?php echo admin_url('expert_lectures'); ?>"> Expert Lectures</a>
                <span class="active-arrow"></span>
            </li>
            <li class="<?php echo $menu_active['survey'] ?>">
                <a href="<?php echo admin_url('survey'); ?>"> Survey</a>
                <span class="active-arrow"></span>
            </li>
            <!-- <li class="<?php echo $menu_active['daily_news'] ?>">
                <a href="<?php echo admin_url('daily_news'); ?>"> Daily News Bulletin</a>
                <span class="active-arrow"></span>
            </li> -->
            <?php /* ?><li class="<?php echo $menu_active['generate_test'] ?>">
                <a href="<?php echo admin_url('generate_test'); ?>"> Generate test</a>
                <span class="active-arrow"></span>
            </li><?php */ ?>
            <!-- active tab end -->
        </ol>
    </section>
    <!-- MAIN TAB --> <!-- END --> 
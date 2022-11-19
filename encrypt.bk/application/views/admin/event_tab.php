<?php 
    $menu_active = array('basic' => '', 'participants' => '' );
    $menu_active[$this->router->fetch_method()] = 'active';
?>

<!-- MAIN TAB --> <!-- STARTS -->
    <section class="courses-tab base-cont-top"> 
        <ol class="nav nav-tabs offa-tab">
            <!-- active tab start -->
            <li class="<?php echo $menu_active['basic'] ?>">
                <a href="<?php echo admin_url('event/basic/').($event["id"]); ?>">SETTINGS</a>
                <span class="active-arrow"></span>
            </li>
            <!-- active tab end -->
            <?php /* ?><li class="<?php echo $menu_active['participants'] ?>">
                <a href="<?php echo admin_url('event/participants/').($event["id"]); ?>">Event Participants</a>
                <span class="active-arrow"></span>
            </li><?php */ ?>
            <!-- active tab end -->
        </ol>
    </section>
    <!-- MAIN TAB --> <!-- END --> 
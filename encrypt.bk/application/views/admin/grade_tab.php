<?php 
    $menu_active = array('basic' => '', 'participants' => '' );
    $menu_active[$this->router->fetch_method()] = 'active';
?>

<!-- MAIN TAB --> <!-- STARTS -->
    <section class="courses-tab base-cont-top"> 
        <ol class="nav nav-tabs offa-tab">
            <!-- active tab start -->
            <li class="<?php echo $menu_active['basic'] ?>">
                <a href="<?php echo admin_url('grade/basic/').base64_encode($grade["id"]); ?>">Settings</a>
                <span class="active-arrow"></span>
            </li>
        </ol>
    </section>
    <!-- MAIN TAB --> <!-- END --> 
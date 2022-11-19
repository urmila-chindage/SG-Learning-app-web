<?php 
$methods = array(
);
$methods[$this->router->fetch_method()] = 'class="active"';
?>
<?php include_once "page_menu.php"; ?>

<div class="nav-group pad0 breadcrump-xs-hidden">
    <div class="container container-altr">
        <div class="container-reduce-width">            
        <ul class="nav nav-tabs my-dashboard-ul custom-breadcrumb-height">
        <li <?php echo isset($methods['index'])?$methods['index']:'' ?> ><a  href="<?php echo site_url('dashboard')?>"><?php echo lang('dashboard') ?></a></li>
        <li <?php echo isset($methods['courses'])?$methods['courses']:'' ?> ><a  href="<?php echo site_url('dashboard/courses')?>"><?php echo lang('courses_db') ?></a></li>
        <!-- <li <?php //echo isset($methods['my_bundles'])?$methods['my_bundles']:'' ?> ><a  href="<?php //echo site_url('dashboard/my_bundles')?>"><?php //echo 'My Bundles' ?></a></li> -->
            <?php unset($methods['index']); ?>
            <?php unset($methods['courses']); ?>
            <?php //unset($methods['my_bundles']); ?>
            <?php /* ?><?php foreach($methods as $method => $class): ?>
                <li <?php echo $class ?> ><a  href="<?php echo site_url('dashboard/'.$method)?>"><?php echo lang($method) ?></a></li>
            <?php endforeach; ?><?php */ ?>
        </ul>
       </div>
    </div>
</div>


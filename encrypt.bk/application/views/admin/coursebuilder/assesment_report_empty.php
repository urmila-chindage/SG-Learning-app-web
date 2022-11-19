<section class="courses-tab base-cont-top-nosidebar">
    <ol class="nav nav-tabs offa-tab">
        <!-- active tab start -->
        <li class="active">
            <a href="<?php echo admin_url('coursebuilder/report') ?>"> <?php echo lang('assesments'); ?> Report</a>
            <span class="active-arrow"></span>
        </li>
        <li class="">
                <a href="<?php echo admin_url('report/excel_report') ?>">Excel Report</a>
                <span class="active-arrow"></span>
        </li>
        <?php $adminn = $this->auth->get_current_user_session('admin'); ?>
        <?php if(in_array($adminn['id'], array(1, 2))): ?>
        <li class="">
                    <a href="<?php echo admin_url('wishlist') ?>">Wishlist Report</a>
                    <span class="active-arrow"></span>
        </li>
        <?php endif; ?>
        <!-- active tab end -->
        <!-- <li >
            <a href="#!.">Game Report</a>
            <span class="active-arrow"></span>
        </li> -->
    </ol>
</section>
<section class="content-wrap base-cont-top-nosidebar ">
	<div class="container-fluid nav-content nav-cntnt100">
        <div class="row">
            <div class="rTable content-nav-tbl">
                <div class="rTableRow">
                </div>
            </div>
        </div>
	</div>
    <div class="left-wrap col-sm-12 profile-wrap report-wrap">

        <div class="container-fluid">
        <div class="col-sm-12 marg-top10">
            <table class="rTable table-with-border width-100p" id="tblcontent">
                <tr class="rTableRow">
                        <td>
                            <div id="popUpMessage" class="alert alert-danger">    <a data-dismiss="alert" class="close">×</a>    No reports to show.</div>
                        </td>
                </tr>
            </table>
            </table>
        </div>
        </div>
    </div>
</section>

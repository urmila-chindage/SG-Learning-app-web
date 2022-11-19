<?php include_once "header.php";?>
<style>
.btn-group.lecture-control { margin: 0px 8px 0px 0px; }
a,label{cursor: default !important;}
.rTableRow { cursor: default !important;}
.promocodecopy{
    cursor: copy !important;
}
.text-green{
    cursor: pointer !important;
}

.c-pointer{
    cursor: pointer !important;
}

/* Tooltip container */
/* .tooltips {
  position: relative;
  display: inline-block;
}
.tooltips .tooltiptext {
  visibility: hidden;
  width: 120px;
  background-color: black;
  color: #fff;
  text-align: center;
  padding: 5px 0;
  border-radius: 6px;
  position: absolute;
  z-index: 1;
}
.tooltips:hover .tooltiptext {
  visibility: visible;
} */
</style>

<?php
$fullwidth_class = 'nopad';
if(in_array($this->__access['add'], $this->__promocode_privilege)):
$fullwidth_class = ''; 
?>
<div class="right-wrap base-cont-top container-fluid pull-right">
    <div class="row">
        <div class="col-sm-12">
            <a href="javascript:void(0);" data-toggle="modal" data-target="#new_promocode" id="create_new_promocode" class="btn btn-big btn-green full-width-btn c-pointer" >
                <svg xmlns="http://www.w3.org/2000/svg" fill="#fff" height="24" stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" width="24" style=" vertical-align: middle;"><line x1="12" x2="12" y1="5" y2="19"></line><line x1="5" x2="19" y1="12" y2="12"></line></svg>
                <?php echo lang('promocode') ?>
            </a>
        </div>
        <!--<div class="col-sm-12">
            <a href="javascript:void(0);" data-toggle="modal" data-target="#new_promocode" id="generate_new_promocode" class="btn btn-big btn-green full-width-btn" >
                <svg xmlns="http://www.w3.org/2000/svg" fill="#fff" height="24" stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" width="24" style=" vertical-align: middle;"><line x1="12" x2="12" y1="5" y2="19"></line><line x1="5" x2="19" y1="12" y2="12"></line></svg>
                <?php //echo lang('bulk_promocode') ?>
            </a>
        </div>-->
    </div>
    
    
<div class="row instruction-sidebar">
    <br>
    <div class="list-group test-listings">
      <a href="javascript:void(0)" class="list-group-item active" style="border-radius: 0;">
        <span class="font15">Instructions</span>
      </a>
      <a href="javascript:void(0)" class="list-group-item link-style">
          <span class="green-span"><i class="icon icon-ok-circled"></i></span>          
          <span class="listing-text"><b>For creating a new discount coupon, click on the above green button and fill all mandatory field required.</b></span></a>
      <a href="javascript:void(0)" class="list-group-item link-style">
          <span class="green-span"><i class="icon icon-ok-circled"></i></span> 
          <span class="listing-text"><b>On clicking the coupon it will be directly copied to the clipboard.</b></span></a>

      <a href="javascript:void(0)" class="list-group-item link-style">
          <span class="green-span"><i class="icon icon-ok-circled"></i></span> 
          <span class="listing-text"><b>On clicking the usage count of each coupon, the system shall redirect you to the discount coupon usage report respectively.</b></span></a>

    </div>
    <!--  Adding list group  --> <!-- END  -->
</div>
</div>



<?php endif; ?>
<section class="content-wrap base-cont-top <?php echo $fullwidth_class; ?>">
    <div class="container-fluid nav-content nav-course-content">
        <div class="row">
            <div class="rTable content-nav-tbl" style="">
                <div class="rTableRow d-flex align-center justify-between">
                    <div class="rTableCell">
                        <a href="javascript:void(0)" class="select-all-style"><label class="c-pointer"> <input class="promocode-checkbox-parent c-pointer" type="checkbox"><?php echo lang('select_all') ?></label><span id="selected_promocode_count"></span></a>
                    </div>

                    <div class="rTableCell dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" id="filter_dropdown_text">All Discount Coupons<span class="caret"></span></a>
                        <ul class="dropdown-menu white">
                            <li><a href="javascript:void(0)" id="filer_dropdown_list_all" class="c-pointer" onclick="filter_promocodes_by('all')">All Discount Coupons</a></li>
                            <li><a href="javascript:void(0)" id="filer_dropdown_list_active" class="c-pointer" onclick="filter_promocodes_by('active')">Active Discount Coupons</a></li>
                            <li><a href="javascript:void(0)" id="filer_dropdown_list_inactive" class="c-pointer" onclick="filter_promocodes_by('inactive')">Inactive Discount Coupons</a></li>
                            <li><a href="javascript:void(0)" id="filer_dropdown_list_expired" class="c-pointer" onclick="filter_promocodes_by('expired')">Expired Discount Coupons</a></li>
                        </ul>
                    </div>

                    <div class="rTableCell">
                        <div class="input-group">
                            <input type="text" class="form-control srch_txt" id="promocode_keyword" placeholder="Discount Coupon Name" />
                            <span id="searchclear">&times;</span>
                            <a class="input-group-addon" id="basic-addon2">
                                <i class="icon icon-search"> </i>
                            </a>
                        </div> 
                    </div>

                    <div class="rTableCell" >
                        <div class="btn-group lecture-control m-0" style="display:none;" id="promocode_bulk">
                            <span class="dropdown-tigger" data-toggle="dropdown" style="padding: 3px 15px;">
                                <span class='label-text'>
                                    <?php echo lang('bulk_action') ?>  <!-- <span class="icon icon-down-arrow"></span> -->
                                </span>
                                <span class="tilder"></span>
                            </span>
                            <ul class="dropdown-menu pull-right" role="menu">
                                <li>
                                    <a href="javascript:void(0)" class="c-pointer" onclick="deletePromocodeBulk()" ><?php echo lang('delete') ?></a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)" class="c-pointer" onclick="changePromocodeStatusBulk('<?php echo lang('are_sure') . ' ' . lang('activate_selected_promocodes') . ' ?' ?>', '1')" ><?php echo lang('account_activate') ?></a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)" class="c-pointer" onclick="changePromocodeStatusBulk('<?php echo lang('are_sure') . ' ' . lang('deactivate_selected_promocodes') . ' ?' ?>', '0')" ><?php echo lang('account_deactivate') ?></a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="rTableCell d-flex align-center" style="margin-top: -2px;">
                        <button class="pull-right btn btn-green selected c-pointer" onclick="exportPromocodeReport();">EXPORT</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="left-wrap pad0">
        <div class="container-fluid">
            <div class="row">   
                <div class="col-sm-12 course-cont-wrap" id="show_message_div"> 
                    <div class="pull-right">
                       <h4 class="right-top-header course-count"><span id="total-promocodes"></span></h4>
                    </div>
                    <div class="table course-cont only-course rTable" id="promocode_row_wrapper"> 
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div id="pagination_wrapper">
        </div>
    </div>
</section>
<?php include_once 'footer.php';?>
<link rel="stylesheet" href="<?php echo assets_url() ?>css/jquery-ui.css">
<script type="text/javascript" src="<?php echo assets_url() ?>js/jquery-ui.min.js"></script>
<script type="text/javascript">
var __promocodes            = atob('<?php echo base64_encode(json_encode($promocodes)) ?>');
var __limit                 = '<?php echo $limit ?>';
var __totalPromocodes       = '<?php echo $total_promocodes; ?>';
var __offset                = <?php echo isset($offset)?$offset:'1'; ?>;
const __previlages__        = atob('<?php echo base64_encode(json_encode($this->__promocode_privilege)) ?>');
</script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/promocode.js"></script>

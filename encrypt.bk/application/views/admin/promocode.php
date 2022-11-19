<?php include_once "header.php";?>
<?php include_once "promocode_tab.php";?>Discount Coupons
<style>
section.base-cont-top.courses-tab {height: 47px !important;}
.courses-tab ol.nav li {border-bottom: unset !important;}
</style>
<section class="content-wrap base-cont-top-heading">
    <div class="left-wrap col-sm-12 pad0">
        <div class="container-fluid course-create-wrap">
            <div class="row-fluid course-cont">
                <div class="col-sm-12">
                    <div class="form-horizontal">
                        <form class="form-horizontal" id="promocode_form" method="post" action="<?php echo admin_url('promo_code/save_promocode').$promocode['id']; ?>">
                        <input type="hidden" id="promocode_creation_type" name="promocode_creation_type" value="<?php echo $promocode['pc_type']; ?>">
                        <input type="hidden" id="promocode_created_date" name="promocode_created_date" value="<?php echo $promocode['pc_created_date']; ?>">
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <?php echo lang('promocode_name') ?> * : 
                                    <input type="text" class="form-control" maxlength="10" placeholder="eg: Promocode Name" name="promocode_name" id="promocode_name" value="<?php echo htmlentities($promocode['pc_promo_code_name']) ?>" />
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <?php echo lang('promocode_description') ?> * : 
                                    <textarea maxlength="1000" class="form-control" id="promocode_description" name="promocode_description" placeholder="eg : This is the description for this promocode." value=""><?php echo $promocode['pc_description']; ?></textarea>
                                </div>
                            </div>
                            <?php if($promocode['pc_type'] == 0): ?>
                            <div class="form-group" id=pc_user_permission_section>
                                <div class="col-sm-8">
                                    <label><?php echo lang('pc_user_type') ?> *:</label>
                                    <div>
                                        <label class="pad-top10 pad0 col-sm-6">
                                            <input type="radio" <?php echo (($promocode['pc_user_permission'] == '0')?'checked="checked"':'') ?> name="promocode_user_permission" value="0">
                                            <span><?php echo lang('open_toall'); ?></span>
                                        </label>
                                        <label class="pad-top10 pad0 col-sm-6">
                                            <input type="radio" <?php echo (($promocode['pc_user_permission'] == '1')?'checked="checked"':'') ?> name="promocode_user_permission" value="1">
                                            <span><?php echo lang('open_tonuser'); ?></span>
                                        </label>
                                    </div>
                                </div>
                                <?php
                                $userlimit_show     = '';
                                if($promocode['pc_user_permission'] == 0)
                                {
                                    $userlimit_show = 'style="display:none;"'; 
                                }
                                ?>
                                <div class="col-sm-4" id="user_limit_section" <?php echo $userlimit_show; ?>>
                                    <label><?php echo lang('number_ofusers') ?> *:</label>
                                    <input type="number"  oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="3" onkeypress="return isNumber(event)" placeholder="eg: 50" name="promocode_user_limit" id="promocode_user_limit" class="form-control" value="<?php echo $promocode['pc_user_limit']; ?>">
                                </div>
                            </div>
                            <?php endif; ?>
                            <div class="form-group">
                                <div class="col-sm-8">
                                    <label><?php echo lang('pc_discount_type') ?> *:</label>
                                    <div>
                                        <label class="pad-top10 pad0 col-sm-6">
                                            <input type="radio" <?php echo (($promocode['pc_discount_type'] == '0')?'checked="checked"':'') ?> name="promocode_discount_type" value="0">
                                            <span><?php echo lang('percentage_discount'); ?></span>
                                        </label>
                                        <label class="pad-top10 pad0 col-sm-6">
                                            <input type="radio" <?php echo (($promocode['pc_discount_type'] == '1')?'checked="checked"':'') ?> name="promocode_discount_type" value="1">
                                            <span><?php echo lang('flat_discount'); ?></span>
                                        </label>
                                    </div>
                                </div>
                                <?php
                                if($promocode['pc_discount_type'] == 0)
                                {
                                   $discount_label    =  'Percentage Rate*:';
                                }
                                else
                                {
                                    $discount_label    =  'Discount Rate*:';
                                }
                                ?>
                                <div class="col-sm-4" id="promocode_discount_section">
                                    <label id="promocode_discount_label"><?php echo $discount_label; ?></label>
                                    <input type="number" placeholder="eg: 50" name="promocode_discount_rate" id="promocode_discount_rate" class="form-control" value="<?php echo $promocode['pc_discount_rate']; ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-4">
                                    <label><?php echo lang('expiry_date') ?> *:</label>
                                    <input placeholder="dd-mm-yyyy" type="text" id="promocode_expiry_date" name="promocode_expiry_date" class="form-control" readonly="" value="<?php echo date('d-m-Y',strtotime($promocode['pc_expiry_date'])); ?>" style="background: #fff;">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <input type="button" onclick="savePromocode()" class="pull-right btn btn-green marg10" value="SAVE">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php include_once 'footer.php';?>
<link rel="stylesheet" href="<?php echo assets_url() ?>css/jquery-ui.css">
<script type="text/javascript" src="<?php echo assets_url() ?>js/jquery-ui.min.js"></script>
<script>
$(document).ready(function(){
    $('input:radio[name="promocode_user_permission"]').change(function(){
        if ($(this).val() == '1') {
            $('#user_limit_section').show();
        }else{
            $('#user_limit_section').hide();
        }
    });
    $('input:radio[name="promocode_discount_type"]').change(function(){
        if ($(this).val() == '1') {
            $('#promocode_discount_label').html('Discount Rate*:');
        }else{
            $('#promocode_discount_label').html('Percentage Rate*:');
        }
    });
});

function isNumber(evt) {
    evt = (evt) ? evt : window.event;
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode > 31 && (charCode < 48 || charCode > 57)) {
        return false;
    }
    return true;
}

$(function(){
    var today = new Date();
    $("#promocode_expiry_date").datepicker({
        language: 'en',
        minDate: today,
        dateFormat: 'dd-mm-yy',
        autoClose: true
    });
});

function savePromocode() {
    var promocodeCreationType     = $.trim($('#promocode_creation_type').val());
    var promocodeName             = $.trim($('#promocode_name').val());
    var promocodeDescription      = $.trim($('#promocode_description').val());
    var promocodeUserPermission   = $.trim($('input[name="promocode_user_permission"]:checked').val());
    var promocodeUserLimit        = $.trim($('#promocode_user_limit').val());
    var promocodeDiscountType     = $.trim($('input[name="promocode_discount_type"]:checked').val());
    var promocodeDiscountRate     = $.trim($('#promocode_discount_rate').val());
    var promocodeExpiryDate       = $.trim($('#promocode_expiry_date').val());

    var message         = [];

    if(promocodeName == '') {
        message.push('Enter Promocode Name.');
    }
    if(promocodeDescription == '') {
        message.push('Enter Promocode Description.');
    }
    if(promocodeCreationType == 0) {
        if(promocodeUserPermission == '') {
            message.push('Choose User Type.');
        }
        if(promocodeUserPermission == 1){
            if(promocodeUserLimit == '' || promocodeUserLimit <= '0') {
                message.push('Enter Number of Users.');
            }
        }
    }
    if(promocodeDiscountType == '') {
        message.push('Choose Discount Type.');
    }
    if(promocodeDiscountType == 0) {
        if(promocodeDiscountRate == '' || promocodeDiscountRate <= '0') {
            message.push('Enter Percentage Rate.');
        }
        if(promocodeDiscountRate > 100) {
            message.push('Enter Valid Percentage Rate.');
        }
    }
    if(promocodeDiscountType == 1) {
        if(promocodeDiscountRate == '' || promocodeDiscountRate <= '0') {
            message.push('Enter Discount Rate.');
        }
    }
    if(promocodeExpiryDate == '') {
        message.push('Enter Promocode Expiry Date.');
    }

    if(message.length > 0 ) {
            var messageObject = {
                    'body': message.join('<br />'),
                    'button_yes': 'OK',
                };
            callback_warning_modal(messageObject);
            scrollToTopOfPage();
        } else {
           $('#promocode_form').submit();
        }
}
</script>
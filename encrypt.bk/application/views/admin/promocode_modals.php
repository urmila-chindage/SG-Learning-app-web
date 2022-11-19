<div class="modal fade" id="new_promocode" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-small" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon icon-cancel-1"></span></button>
                <h4 class="modal-title" id="create_box_title"><?php echo lang('create_promocode') ?></h4>
                <h4 class="modal-title" id="generate_box_title"><?php echo lang('create_bulk_promocode') ?></h4>
            </div>
            <input type="hidden" id="promocode_creation_type" name="promocode_creation_type" value="">
            <div class="modal-body">
                <div class="form-group clearfix">
                    <div class="col-sm-12">
                        <label><?php echo lang('promocode_name') ?> *:</label>
                        <input type="text" maxlength="10" name="promocode_name" id="promocode_name" placeholder="eg: ASQWE" class="form-control" style="text-transform: uppercase">
                    </div>
                </div>
                <!--<div class="form-group clearfix">
                    <div class="col-sm-12">
                        <label><?php //echo lang('promocode_description') ?> *:</label>
                        <textarea maxlength="1000" class="form-control" id="promocode_description" name="promocode_description" placeholder="eg : This is the description for this Discount Coupon."></textarea>
                    </div>
                </div>-->
                <div class="form-group clearfix" id=pc_user_permission_section>
                    <div class="col-sm-8">
                        <label><?php echo lang('pc_usage_type') ?> *:</label>
                        <div>
                            <label class="pad-top10 pad0 col-sm-6 c-pointer">
                                <input type="radio" name="promocode_user_permission" value="0">
                                <span><?php echo lang('open_toall'); ?></span>
                            </label>
                            <label class="pad-top10 pad0 col-sm-6 c-pointer">
                                <input type="radio" name="promocode_user_permission" value="1">
                                <span><?php echo lang('open_tonuser'); ?></span>
                            </label>
                        </div>
                    </div>
                    <div class="col-sm-4" id="user_limit_section">
                        <label><?php echo lang('number_ofusers') ?> *:</label>
                        <input type="number" min="1" disabled="true"  oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="3" onkeypress="return isNumber(event)" placeholder="eg: 50" name="promocode_user_limit" id="promocode_user_limit" class="form-control">
                    </div>
                </div>
                <div class="form-group clearfix">
                    <div class="col-sm-8">
                        <label><?php echo lang('pc_discount_type') ?> *:</label>
                        <div>
                            <label class="pad-top10 pad0 col-sm-6 c-pointer">
                                <input type="radio" name="promocode_discount_type" value="0">
                                <span><?php echo lang('percentage_discount'); ?></span>
                            </label>
                            <label class="pad-top10 pad0 col-sm-6 c-pointer">
                                <input type="radio" name="promocode_discount_type" value="1">
                                <span><?php echo lang('flat_discount'); ?></span>
                            </label>
                        </div>
                    </div>
                    <div class="col-sm-4" id="promocode_discount_section">
                        <label id="promocode_discount_label"></label>
                        <input type="number" min="1" max="99.98" step="any" placeholder="eg: 50" name="promocode_discount_rate" id="promocode_discount_rate" class="form-control">
                    </div>
                </div>
                <div class="form-group clearfix">
                    <div class="col-sm-6" id="pc_count_section">
                        <label><?php echo lang('pc_count_generate') ?> *:</label>
                        <input type="number" min="1"  oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="3" onkeypress="return isNumber(event)" placeholder="eg: 50" name="promocode_count" id="promocode_count" class="form-control">
                    </div>
                    <div class="col-sm-4">
                        <label><?php echo lang('expiry_date') ?> *:</label>
                        <input placeholder="dd-mm-yyyy" type="text" id="promocode_expiry_date" name="promocode_expiry_date" class="form-control" readonly="" style="background: #fff;">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-red" data-dismiss="modal" ><?php echo lang('cancel') ?></button>
                <button type="button" class="btn btn-green" onclick="savePromocode()"><?php echo lang('create') ?></button>
            </div>
        </div>
    </div>
</div>
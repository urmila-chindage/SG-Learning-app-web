<style type="text/css">
.payment-gateway-wrapper {
    padding: 18px;
}
.accordion-container .panel{
    border: 0px;
    margin-bottom: 1px;
    padding: 0px;
}
</style>
<h3 class="text-center social-heading">Payment Gateway Settings</h3>
<div class="accordion-container">  
<?php 
if(isset($payment_settings) && !empty($payment_settings)):
foreach ($payment_settings as $payment_key => $payment_setting): ?>
    <button class="accordion" type="button">
        <span id="button_form_<?php echo $payment_key; ?>">
        <?php if($payment_setting['basic']['active'] ==1) { ?>
            <span class="icon-tick">
                <svg id="Layer_1" style="enable-background:new 0 0 512 512; vertical-align: super;" version="1.1" viewBox="0 0 512 512" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><style type="text/css">
                .st0{fill:#41AD49;}</style><g><polygon class="st0" points="434.8,49 174.2,309.7 76.8,212.3 0,289.2 174.1,463.3 196.6,440.9 196.6,440.9 511.7,125.8 434.8,49     "></polygon></g>
                </svg>
            </span>
        <?php } else { ?>
            <span class="icon-close">
                &times;
            </span>
        <?php } ?>
        </span>
        <span class="step-title"><?php echo $payment_setting['basic']['title']; ?></span>
    </button>
    <div class="panel">
      <div class="payment-gateway-wrapper col-md-12">
        <form method="post" id="form_<?php echo $payment_key; ?>">
            <div class="payment_message"></div>
            <div>
                <section class="model-check pull-right">
                    <div class="cust-checkbox">
                        <input type="checkbox" class="show-checkbox"  name="has_enable" value="<?php echo $payment_setting['basic']['active']; ?>" <?php echo (($payment_setting['basic']['active'] == 1)?"checked":"") ?> />
                        <label></label>
                    </div>
                </section>
            </div>  
 
         <?php 
         if(isset($payment_setting['creditionals']) && !empty($payment_setting['creditionals'])):
         foreach($payment_setting['creditionals'] as $key => $value): ?>
            <div class="show-checkbox-content">
                <div class="form-group">
                    <div>
                        <span class="settings-text"><?php echo $key; ?>* :</span>
                        <input type="text" class="form-control payment-fields" data-label="<?php echo base64_encode($key) ?>" name="<?php echo $payment_key; ?>[<?php echo $key; ?>]" value="<?php echo $value; ?>" />
                    </div>
                </div>                            
            </div>
         <?php 
         endforeach; 
         endif; ?> 
            <input type="hidden" name="payment_gateway_type" value="<?php echo $payment_key; ?>" />
            <div class="save-btn">
                <input type="button" name="save" value="SAVE" class="pull-right btn btn-green" onclick="savePaymentGatewayConfigs('form_<?php echo $payment_key; ?>')" />
                <input type="button" name="clear" value="CLEAR" class="pull-right btn btn-red" onclick="clearPaymentGatewayConfigs('form_<?php echo $payment_key; ?>')" />
            </div>  
        </form>
      </div>
    </div>
<?php 
endforeach; 
endif; ?>
</div>
